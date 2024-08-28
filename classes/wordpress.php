<?php if ( !defined('WPINC') ) die();

class Transliteration_Wordpress extends Transliteration {
    
    public function __construct() {
		$this->add_filter('sanitize_user', 'allow_cyrillic_usernames', 10, 3);
		$this->add_filter('body_class', 'add_body_class', 10, 1);
		$this->transliterate_rss_atom();
		$this->transliterate_widgets();
		$this->transliterate_permalinks();
		
		if(get_rstr_option('media-transliteration', 'yes') == 'yes'){
			$this->add_filter('wp_handle_upload_prefilter', 'upload_prefilter', (PHP_INT_MAX-1), 1);
			$this->add_filter( 'sanitize_file_name', 'sanitize_file_name', (PHP_INT_MAX-1) );
			$this->add_filter( 'wp_unique_filename', 'sanitize_file_name', (PHP_INT_MAX-1) );
		}
    }
	
	public function allow_cyrillic_usernames($username, $raw_username, $strict) {
		if (get_rstr_option('allow-cyrillic-usernames', 'no') === 'no') {
			return $username;
		}

		// Osiguravamo da je $raw_username string, čak i ako je NULL
		$username = wp_strip_all_tags($raw_username ?? '');
		$username = remove_accents($username);

		// Uklanjamo oktete
		$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
		$username = preg_replace('/&.+?;/', '', $username); // Uklanjamo entitete

		// Ako je strogi mod, smanjujemo na ASCII i ćirilične karaktere radi maksimalne prenosivosti
		if ($strict) {
			$username = preg_replace('|[^a-zа-я0-9 _.\-@]|iu', '', $username);
		}
		
		$username = trim($username);

		// Konsolidujemo uzastopne razmake
		$username = preg_replace('|\s+|', ' ', $username);

		return $username;
	}
	
	public function add_body_class($classes){
		if(get_rstr_option('enable-body-class', 'no') == 'no') {
			return;
		}
		$script = Transliteration_Utilities::get_current_script();
		//body class based on the current script - cyr, lat
		$classes[] = 'rstr-' . $script;
		$classes[] = 'transliteration-' . $script;
		$classes[] = $script;
		return $classes;
	}
	
	private function transliterate_rss_atom() {
		if (get_rstr_option('enable-rss', 'no') === 'no' || get_rstr_option('transliteration-mode', 'cyr_to_lat') === 'none') {
			return;
		}
		
		$priority = PHP_INT_MAX - 100;
		$actions = [
			'rss_head', 'rss_footer',
			'rss2_head', 'rss2_footer',
			'rdf_head', 'rdf_footer',
			'atom_head', 'atom_footer',
		];
		foreach ($actions as $action) {
			$this->add_action($action, 'rss_output_buffer_' . (strpos($action??'', '_head') ? 'start' : 'end'), $priority);
		}		
	}
	
	private function transliterate_widgets() {
		if (get_rstr_option('force-widgets', 'no') === 'no' || get_rstr_option('transliteration-mode', 'cyr_to_lat') === 'none') {
			return;
		}
		
		$priority = PHP_INT_MAX - 100;
		$this->add_action('dynamic_sidebar_before', 'rss_output_buffer_start', $priority);
		$this->add_action('dynamic_sidebar_after', 'rss_output_buffer_end', $priority);
	}
	
	public function rss_output_buffer_start() {
		$this->ob_start('rss_output_buffer_callback');
	}
	
	public function rss_output_buffer_callback( $buffer ) {
		return Transliteration_Controller::get()->transliterate($buffer);
	}

	public function rss_output_buffer_end() {
		if (ob_get_level() > 0) {
			ob_end_flush();
		}
	}
	
	public function transliterate_permalinks() {
		$priority = PHP_INT_MAX - 100;
		
		$permalink_transliteration = (get_rstr_option('permalink-transliteration', 'yes') == 'yes');
		$ser_cyr_to_lat_slug = ($permalink_transliteration && Transliteration_Utilities::get_locale() == 'sr_RS');
		if($ser_cyr_to_lat_slug) $permalink_transliteration = false;

		if($permalink_transliteration){
			$this->add_filter('sanitize_title', 'force_permalink_to_latin', $priority, 1);
			$this->add_filter('the_permalink', 'force_permalink_to_latin', $priority, 1);
			$this->add_filter('wp_unique_post_slug', 'force_permalink_to_latin', $priority, 1);
			$this->add_filter('permalink_manager_filter_default_post_uri', 'force_permalink_to_latin', $priority, 1);
			$this->add_filter('permalink_manager_filter_default_term_uri', 'force_permalink_to_latin', $priority, 1);
			$this->add_filter('wp_insert_post_data', 'force_permalink_to_latin_on_save', $priority, 2);
		}
	}
	
	public function force_permalink_to_latin ($permalink) {
		return Transliteration_Mode::get()->force_permalink_to_latin($permalink);
	}
	
	
	public function force_permalink_to_latin_on_save ($data, $postarr) {
		if( isset($data['post_name']) ) {
			$data['post_name'] = $this->force_permalink_to_latin( $data['post_name'] );
		}
		
		return $data;
	}
	
	/*
	 * Prefiler for the upload
	*/
	public function upload_prefilter ($file) {
		$file['name']= $this->sanitize_file_name($file['name']);
		return $file;
	}

	/*
	 * Sanitize file name
	*/
	public function sanitize_file_name($filename){
		$delimiter = get_rstr_option('media-delimiter', 'no');

		if($delimiter != 'no') {
			$name = $this->force_permalink_to_latin($filename);
			$name = preg_split("/[\-_~\s]+/", $name);
			$name = array_filter($name);

			if(!empty($name)) {
				return join($delimiter, $name);
			} else {
				return $filename;
			}
		}
		
		return $filename;
	}
	
}