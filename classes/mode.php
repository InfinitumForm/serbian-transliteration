<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Mode', false) ) : class Transliteration_Mode extends Transliteration {
	
	private $mode = NULL;
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		// Load transliteration
		$this->load_mode();
		// Apply transliteration filters
		$this->apply_filters();
    }
	
	/*
	 * Get current instance
	 */
	private static $instance = NULL;
	public static function get() {
		if( NULL === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}	
	
	/*
	 * The current mode
	 */
	public function mode( $mode = NULL ) {
		static $mode_class = [];
		
		$cache_key = $mode ?? 0;
		
		if( array_key_exists($cache_key, $mode_class) ) {
			return $mode_class[$cache_key];
		}
		
		$available_modes = array_keys( Transliteration_Utilities::plugin_mode() );
		
		if( $mode && in_array($mode, $available_modes) ) {
			$current_mode = $mode;
		} else {
			$current_mode = get_rstr_option('mode', 'light');
		}
		
		if( $current_mode ) {
			if( class_exists( $mode = 'Transliteration_Mode_' . ucfirst($current_mode) ) ) {
				$mode_class[$cache_key] = $mode;
			}
		}
		
		return $mode_class[$cache_key] ?? [];
	}
	
	/*
	 * Load the current mode
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        2.0.0
	 */
	public function filters() {
		static $filters = NULL;
		
		if( NULL === $filters ) {
			if( get_rstr_option('transliteration-mode', 'light') == 'none' ) {
				$filters = [];
				$filters = apply_filters('transliteration_mode_filters', $filters);
				$filters = apply_filters_deprecated('rstr/transliteration/exclude/filters', [$filters], '2.0.0', 'transliteration_mode_filters');
			} else {
				$filters = $this->mode->filters();
				$filters = apply_filters('transliteration_mode_filters', $filters);
				$filters = apply_filters('transliteration_mode_filters_' . $this->mode::MODE, $filters);
				
				$filters = apply_filters_deprecated('rstr/transliteration/exclude/filters', [$filters], '2.0.0', 'transliteration_mode_filters');
				$filters = apply_filters_deprecated('rstr/transliteration/exclude/filters/' . $this->mode::MODE, [$filters], '2.0.0', 'transliteration_mode_filters_' . $this->mode::MODE);
			}
		}
		
		return $filters;
	}
	
	/*
	 * Load the current mode
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        2.0.0
	 */
	private function load_mode() {
		if( empty($this->mode) && ( $mode_class = $this->mode() ) ) {
			$this->mode = $mode_class::get();
		}
	}
	
	/*
	 * Apply filters for current mode
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        2.0.0
	 */
	private function apply_filters() {
		$filters = NULL;
		
		// Is admin, a do special static filters
		if( is_admin() && !wp_doing_ajax() ) {
			if( class_exists('Transliteration_Mode_Admin') && get_rstr_option('avoid-admin', 'no') == 'no' ) {
				$filters = Transliteration_Mode_Admin::get()->filters();
			}
		}
		// On the frontend enable modes
		else {
			$filters = $this->filters();
		}
		
		if( empty($filters) ) {
			return;
		}
		
		if ( $filters ) {
			foreach ($filters as $key => $method) {
				$args = $key === 'gettext' ? 3 : 1;

				if( is_array($method) ) {
					add_filter($key, $method, (PHP_INT_MAX - 100), $args);
				} else if( method_exists($this, $method) ) {
					$this->add_filter($key, $method, (PHP_INT_MAX - 100), $args);
				}
			}
		}
	}
	
	/**
	 * Transliterate any content
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function content( $content, $mode = 'auto', $sanitize_html = true ) {
		return Transliteration_Controller::get()->transliterate($content, $mode, $sanitize_html);
	}
	
	/*
	 * Force to Lat - Transliterate Content (HTML & Text)
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function content__force_lat($content = '') {
		if (empty($content)) {
			return $content;
		}

		if (is_array($content)) {
			return $this->objects($content);
		} elseif (is_string($content)) {
			return $this->content($content, 'cyr_to_lat');
		}

		return $content;
	}
	
	/**
	 * Transliterate text
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function no_html_content( $content ) {
		return Transliteration_Controller::get()->transliterate_no_html($content, (Transliteration_Utilities::is_admin() ? 'cyr_to_lat' : 'auto'));
	}
	
	/*
	 * Transliterate Blog informations
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function bloginfo($output, $show = '') {
		if (!empty($show) && in_array($show, ['name', 'description'])) {
			$output = $this->no_html_content($output);
		}
		
		return $output;
	}
	
	/**
	 * Transliterate Objects
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function objects($data, $mode = 'auto') {
		return $this->transliterate_objects($data, $mode);
	}
	public function transliterate_objects($data, $mode = 'auto') {
		if (is_array($data)) {
			// Ako je data niz, rekurzivno prolazimo kroz sve elemente niza
			foreach ($data as &$value) {
				if (is_array($value) || is_object($value)) {
					$value = $this->transliterate_objects($value, $mode);
				} elseif (is_string($value)) {
					$value = Transliteration_Controller::get()->transliterate($value, $mode);
				}
			}
		} elseif (is_object($data)) {
			// Ako je data objekat, prolazimo kroz sve njegove javne varijable
			foreach ($data as $key => $value) {
				if (is_array($value) || is_object($value)) {
					$data->$key = $this->transliterate_objects($value, $mode);
				} elseif (is_string($value)) {
					$data->$key = Transliteration_Controller::get()->transliterate($value, $mode);
				}
			}
		}

		return $data;
	}
	
	/**
	 * Transliterate WP Terms
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function transliteration_wp_terms($wp_terms) {
		if (empty($wp_terms) || !is_array($wp_terms)) {
			return $wp_terms;
		}

		foreach ($wp_terms as $i => $term) {
			if (is_object($term)) {
				if (isset($term->name) && !empty($term->name)) {
					$wp_terms[$i]->name = $this->content($term->name, (Transliteration_Utilities::is_admin() ? 'cyr_to_lat' : 'auto'));
				}
				if (isset($term->description) && !empty($term->description)) {
					$wp_terms[$i]->description = $this->content($term->description, (Transliteration_Utilities::is_admin() ? 'cyr_to_lat' : 'auto'));
				}
			}
		}

		return $wp_terms;
	}
	
	/**
	 * Force all permalinks to latin
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function force_permalink_to_latin ($permalink) {
		$permalink = rawurldecode($permalink);
		$permalink= Transliteration_Controller::get()->cyr_to_lat_sanitize($permalink);
		return $permalink;
	}
	
	/**
	 * Transliterate Image attributes
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function image_attributes($attributes) {		
		return Transliteration_Controller::get()->transliterate_attributes($attributes, ['alt', 'title']);
	}
	
	/**
	 * Transliterate WP Mails
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function wp_mail ($args) {
		
		if( $args['message'] ?? false ) {
			$args['message'] = $this->content($args['message']);
		}
		
		if( $args['subject'] ?? false ) {
			$args['subject'] = $this->content($args['subject']);
		}
		
		return $args;
	}
	
	/*
	 * Transliterate gettext (HTML & Text)
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function gettext_content($content, $text = '', $domain = '') {
		if (empty($content)) {
			return $content;
		}

		if ( is_array($content) ) {
			return $this->transliterate_objects($content);
		} else if ( is_string($content) ) {
			return $this->content($content);
		}

		return $content;
	}
	
	/*
	 * Fix title parts
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function title_parts( $titles = array() ) {
		foreach ( $titles as $key => $val ) {
			if ( is_string( $val ) && ! is_numeric( $val ) ) {
				$titles[ $key ] = $this->no_html_content( $titles[ $key ] );
			}
		}
		return $titles;
	}
	
	/*
	 * Transliterate Posts results
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function get_posts ($posts) {
		
		foreach($posts as &$post) {
			$post->post_title = $this->content($post->post_title??'');
			$post->post_content = $this->content($post->post_content??'');
			$post->post_excerpt = $this->content($post->post_excerpt??'');
		}
		
		return $posts;
	}
	
	/*
	 * Transliterate JSON
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function transliteration_json_content($json_content) {
		if (empty($json_content)) {
			return $json_content;
		}

		$content = json_decode($json_content, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return $json_content;
		}

		$content = $this->processMessages($content);

		return wp_json_encode($content);
	}

	private function processMessages($content) {
		if (isset($content['locale_data']['messages']) && is_array($content['locale_data']['messages'])) {
			foreach ($content['locale_data']['messages'] as $key => $messages) {
				$content['locale_data']['messages'][$key] = $this->transliterateMessages($messages);
			}
		}
		return $content;
	}

	private function transliterateMessages($messages) {
		if (!is_array($messages)) {
			return $messages;
		}
		
		foreach ($messages as $key => $message) {
			$messages[$key] = $this->no_html_content($message, (Transliteration_Utilities::is_admin() ? 'cyr_to_lat' : 'auto'));
		}
		return $messages;
	}
	
} endif;