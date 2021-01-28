<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Init class
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Init') && class_exists('Serbian_Transliteration')) :
final class Serbian_Transliteration_Init extends Serbian_Transliteration {
	
	private static $instance = NULL;
	
	/**
	 * Get singleton instance of global class
	 * @since     1.0.0
	 * @version   1.0.0
	 */
	private static function get_instance()
	{
		if( NULL === self::$instance )
		{
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	/**
	 * Get singleton instance of global class
	 * @since     1.0.9
	 * @version   1.0.0
	 */
	public function remove_menu_page() {
		global $submenu;
		
		if(isset($submenu['upload.php']) && is_array($submenu['upload.php']))
		{
			foreach($submenu['upload.php'] as $parent=>$locations){
				if(is_array($locations))
				{
					foreach($locations as $i=>$value)
					{
						if(strpos($value, 'rstr-script') !== false) unset($submenu['upload.php'][$parent]);
					}
				}
			}
		}
	}
	
	/**
	 * Set cookie on the first initialization
	 * @since     1.0.9
	 * @version   1.0.0
	 */
	public function set_cookie($options){
		if( !(isset($_COOKIE['rstr_script'])) )
		{
			if(isset($options['first-visit-mode']) && $options['first-visit-mode'] == 'lat')
			{
				$this->setcookie('lat');
			}
			else if(isset($options['first-visit-mode']) && $options['first-visit-mode'] == 'cyr')
			{
				$this->setcookie('cyr');
			}
			else
			{
				if($options['transliteration-mode'] == 'cyr_to_lat') {
					$this->setcookie('lat');
				} else if($options['transliteration-mode'] == 'lat_to_cyr') {
					$this->setcookie('cyr');
				}
			}
		}
	}
	
	public function admin_head(){ ?>
<style>#rstr-script-adder{display:none !important;}</style>
	<?php }
	
	public function wp_head(){ }
	
	/*
	 * Run all dependency in the background
	 */
	public static function run_dependency(){
		$inst = self::get_instance();
		
		/* Transliterate wp-admin
		====================================*/
		if(is_admin())
		{
			// Load options
			$options = get_rstr_option();
			/* Admin transliterations
			=========================================*/
			if($admin_mode_class = $inst->mode(['mode' => 'admin'])) {
				new $admin_mode_class($options);
			}
		}
		
		/* Do special filtering
		====================================*/
		add_filter('rstr/transliteration/exclude/filters', function($filters, $options) {
			if(isset($options['transliteration-filter']) && is_array($options['transliteration-filter'])) {
				
				$only_woo = false;
				if(RSTR_WOOCOMMERCE && isset($options['mode']) && $options['mode'] == 'woocommerce') $only_woo = true;

				foreach($options['transliteration-filter'] as $filter){
					
					if($only_woo && strpos($filter, 'woo') === false){
						unset($filters[$filter]);
					}
					
					if( isset($filters[$filter]) ) {
						unset($filters[$filter]);
						
						if($filter == 'the_excerpt' && isset($filters['get_the_excerpt'])) {
							unset($filters['get_the_excerpt']);
						}
					}
				}
			}
			return $filters;
		}, PHP_INT_MAX, 2);
		
		
		/* Add generator
		====================================*/
		add_filter('the_generator',function($gen, $type){
			if(apply_filters('rstr/transliteration/generator', true))
			{
				switch ( $type )
				{
						case 'html':
								$gen.= PHP_EOL . '<meta name="generator" content="WordPress Transliterator ' . RSTR_VERSION . '">';
								break;
						case 'xhtml':
								$gen.= PHP_EOL . '<meta name="generator" content="WordPress Transliterator ' . RSTR_VERSION . '" />';
								break;
						case 'atom':
								$gen.= PHP_EOL . '<generator uri="https://downloads.wordpress.org/plugin/serbian-transliteration.' . RSTR_VERSION . '.zip" version="' . RSTR_VERSION . '">WordPress Transliterator</generator>';
								break;
						case 'rss2':
								$gen.= PHP_EOL . '<generator>' . esc_url_raw( 'https://downloads.wordpress.org/plugin/serbian-transliteration.' . RSTR_VERSION . '.zip' ) . '</generator>';
								break;
						case 'rdf':
								$gen.= PHP_EOL . '<admin:generatorAgent rdf:resource="' . esc_url_raw( 'https://downloads.wordpress.org/plugin/serbian-transliteration.' . RSTR_VERSION . '.zip' ) . '" />';
								break;
						case 'comment':
								$gen.= PHP_EOL . '<!-- generator="WordPress Transliterator/' . RSTR_VERSION . '" -->';
								break;
						case 'export':
								$gen.= PHP_EOL . '<!-- generator="WordPress Transliterator/' . RSTR_VERSION . '" created="' . gmdate( 'Y-m-d H:i' ) . '" -->';
								break;
				}
			}
			return $gen;
		}, 10, 2);
		
		/* Load menu
		====================================*/
		if(!class_exists('Serbian_Transliteration_Menu') && file_exists(RSTR_INC . '/Menu.php')) {
			include_once RSTR_INC . '/Menu.php';
			new Serbian_Transliteration_Menu();
		}
		
		/* Load plugins support
		====================================*/
		Serbian_Transliteration_Plugins::includes();
		/* Load themes support
		====================================*/
		Serbian_Transliteration_Themes::includes();
	}
	
	/*
	 * Run plugin on the frontend
	 */
	public static function run () {
		
		// Load instance
		$inst = self::get_instance();
		
		add_action('wp_head', array($inst, 'wp_head'));
		
		// Register taxonomy
		parent::attachment_taxonomies();		
		
		if( !is_admin() )
		{
			$inst->set_current_script();
		}
		else
		{
			// Remove admin menu pages
			add_action('admin_menu', array($inst, 'remove_menu_page'));
			// Add soem scripts
			add_action('admin_head', array($inst, 'admin_head'));
			// Load settings page
			if(!class_exists('Serbian_Transliteration_Settings') && file_exists(RSTR_INC . '/Settings.php')) {
				include_once RSTR_INC . '/Settings.php';
				$Serbian_Transliteration_Settings = new Serbian_Transliteration_Settings();
				new Serbian_Transliteration_Settings_Sidebar( $Serbian_Transliteration_Settings );
				new Serbian_Transliteration_Settings_Content( $Serbian_Transliteration_Settings );
			}
		}
		
		// Load options
		$options = get_rstr_option();
		
		// Load shortcodes
		if(!class_exists('Serbian_Transliteration_Shortcodes') && file_exists(RSTR_INC . '/Shortcodes.php')) {
			include_once RSTR_INC . '/Shortcodes.php';
			new Serbian_Transliteration_Shortcodes($options);
		}

		// Initialize plugin mode
		if(isset($options['mode']) && $options['mode'] && in_array( $options['mode'], array_keys($inst->plugin_mode()), true ) !== false)
		{
			if($options['transliteration-mode'] != 'none')
			{
				// Display alternate links
				if(defined('RSTR_ALTERNATE_LINKS') && RSTR_ALTERNATE_LINKS) {
					$inst->add_action('wp_head', 'alternate_links', 1);
				}
				
				// Set cookie
				$inst->set_cookie($options);
		
				// Include mode class				
				if($mode_class = $inst->mode($options)) {
					if(method_exists($mode_class,'run')) {
						$mode_class::run($options);
					} else {
						throw new Exception(sprintf('The static method "$1%s::$2%s" does not exist or is not correctly defined on the line %3%d', $mode_class, 'run', (__LINE__-2)));
					}
				}
			}

			/* Media upload transliteration
			=========================================*/
			if(isset($options['media-transliteration']) && $options['media-transliteration'] == 'yes'){
				$inst->add_filter('wp_handle_upload_prefilter', 'upload_prefilter', 9999999, 1);
				$inst->add_filter( 'sanitize_file_name', 'sanitize_file_name', 99 );
			}
			
			/* Permalink transliteration
			=========================================*/
			$permalink_transliteration = (isset($options['permalink-transliteration']) && $options['permalink-transliteration'] == 'yes');
			$ser_cyr_to_lat_slug = ($permalink_transliteration && $inst->get_locale() == 'sr_RS' && get_option('ser_cyr_to_lat_slug'));
			if($ser_cyr_to_lat_slug) $permalink_transliteration = false;
			
			if($permalink_transliteration){
				$inst->add_filter('sanitize_title', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('the_permalink', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('wp_unique_post_slug', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('permalink_manager_filter_default_post_uri', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('permalink_manager_filter_default_term_uri', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('wp_insert_post_data', 'force_permalink_to_latin_on_save', 9999999, 2);
			}
			
			/* WordPress search transliteration
			=========================================*/
			if(isset( $options['enable-search'] ) && $options['enable-search'] == 'yes')
			{
				if(!class_exists('Serbian_Transliteration_Search') && file_exists(RSTR_INC . '/Search.php')) {
					include_once RSTR_INC . '/Search.php';
					new Serbian_Transliteration_Search($options);
				}
			}
			
			/* WordPress exlude words
			=========================================*/
			if(isset($options['exclude-latin-words']) && !empty($options['exclude-latin-words']))
			{
				add_filter('rstr/init/exclude/cyr', function($list) use ($options){
					$array = array();
					if($split = preg_split('/[\n|]/', $options['exclude-latin-words']))
					{
						$split = array_map('trim',$split);
						$split = array_filter($split);
						if(!empty($split) && is_array($split))
						{
							$array = $split;
						}
					}
					return array_merge($list, $array);
				});
			}
			
			if(isset($options['exclude-cyrillic-words']) && !empty($options['exclude-cyrillic-words']))
			{
				add_filter('rstr/init/exclude/lat', function($list) use ($options){
					$array = array();
					if($split = preg_split('/[\n|]/', $options['exclude-cyrillic-words']))
					{
						$split = array_map('trim',$split);
						$split = array_filter($split);
						if(!empty($split) && is_array($split))
						{
							$array = $split;
						}
					}
					return array_merge($list, $array);
				});
			}
			
			/* Allows to create users with usernames containing Cyrillic characters
			=========================================*/
			if(isset($options['allow-cyrillic-usernames']) && $options['allow-cyrillic-usernames'] == 'yes')
			{
				add_filter('sanitize_user', function ($username, $raw_username, $strict) {
					$username = wp_strip_all_tags( $raw_username );
					$username = remove_accents( $username );

					// Kill octets
					$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
					$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

					// If strict, reduce to ASCII and Cyrillic characters for max portability.
					if ( $strict ){
						$username = preg_replace( '|[^a-zа-я0-9 _.\-@]|iu', '', $username );
					}
					$username = trim( $username );

					// Consolidate contiguous whitespace
					$username = preg_replace( '|\s+|', ' ', $username );

					return $username;
				}, 10, 3);
			}
		}
	}
}
endif;