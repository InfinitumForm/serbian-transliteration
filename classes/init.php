<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Init', false) ) : class Transliteration_Init extends Transliteration {
    
    public function __construct() {
        // Register the textdomain for the plugin
        $this->add_action('plugins_loaded', 'load_textdomain');
		$this->add_action('template_redirect', 'set_transliteration');
		
		// Register main classes
		$main_classes = apply_filters('transliteration_classes_init', [
			'Transliteration_Themes',
			'Transliteration_Plugins',
			'Transliteration_Filters',
			'Transliteration_Settings',
			'Transliteration_Mode',
			'Transliteration_Controller',
			'Transliteration_Wordpress',
			'Transliteration_Menus',
			'Transliteration_Notifications'
		]);
		
		foreach ($main_classes as $main_class_name) {
			new $main_class_name();
		}
		
		// Register plugin classes
        $classes = apply_filters('transliteration_classes_init', [
			'Transliteration_Rest',
			'Transliteration_Ajax',
			'Transliteration_Email',
			'Transliteration_Search'
		]);
		
		$classes = apply_filters('transliteration_init_classes', $classes);
		
		foreach ($classes as $class_name) {
			new $class_name();
		}
    }
	
	/*
	 * Set transliteration & redirections
	 */
	public function set_transliteration () {
		// URL Selector
		$url_selector = get_rstr_option('url-selector', 'rstr');
		
		// Set REQUEST param
		$request = $_REQUEST[$url_selector] ?? NULL;
		if( in_array($request, apply_filters('rstr/allowed_script', ['cyr', 'lat']), true) !== false ) {
			// Set cookie
			Transliteration_Utilities::setcookie($request);
			// Get current URL
			$url = remove_query_arg($url_selector);
			// Set no-cache headers
			if(get_rstr_option('cache-support', 'no') == 'yes') {
				$url = add_query_arg('_rstr_nocache', uniqid($url_selector . random_int(1000,9999)), $url);
				Transliteration_Utilities::cache_flush();
			} else if(function_exists('nocache_headers')) {
				nocache_headers();
			}
			
			if(wp_safe_redirect($url, 301)) {
				exit;
			}
		}
		
		// Cache control
		if( isset($_REQUEST['_rstr_nocache']) ) {
			// Clear cache
			if(function_exists('nocache_headers')) {
				nocache_headers();
			}
			// Remove cache param
			if(wp_safe_redirect( remove_query_arg('_rstr_nocache'), 301 )) {
				exit;
			}
		}
	}
	
	
	/*
	 * Do translations
	 */
    public function load_textdomain() {
		
        if (is_textdomain_loaded('serbian-transliteration')) {
			return;
		}

		if (!function_exists('is_user_logged_in')) {
			include_once ABSPATH . '/wp-includes/pluggable.php';
		}

		$locale = apply_filters('rstr_plugin_locale', get_user_locale(), 'serbian-transliteration');
		$mofile = sprintf('%s-%s.mo', 'serbian-transliteration', $locale);

		// Prvo proveravamo prevode unutar direktorijuma plugina
		$domain_path = RSTR_ROOT . '/languages';
		$loaded = load_textdomain('serbian-transliteration', path_join($domain_path, $mofile));

		// Ako prevod nije pronađen, proveravamo globalni direktorijum
		if (!$loaded) {
			$domain_path = path_join(WP_LANG_DIR, 'plugins');
			$loaded = load_textdomain('serbian-transliteration', path_join($domain_path, $mofile));
		}

		// Ako ni to ne uspe, proveravamo direktno u WP_LANG_DIR
		if (!$loaded) {
			$loaded = load_textdomain('serbian-transliteration', path_join(WP_LANG_DIR, $mofile));
		}
    }


	/*
	 * Register Plugin Activation
	 */
	public static function register_activation () {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		
		// Unload textdomain before is loaded
		if (is_textdomain_loaded('serbian-transliteration')) {
			unload_textdomain('serbian-transliteration');
		}
		
		// Save version and set activation date
		update_option('serbian-transliteration-version', RSTR_VERSION, false);

		$activation = get_option('serbian-transliteration-activation', []);
		$activation[] = date('Y-m-d H:i:s');
		update_option('serbian-transliteration-activation', $activation);

		// Generate unique ID
		if (!get_option('serbian-transliteration-ID')) {
			add_option('serbian-transliteration-ID', Transliteration_Utilities::generate_token(64));
		}
		
		// Set default options if not set
		$options = get_option('serbian-transliteration', Transliteration_Utilities::plugin_default_options());
		$options = array_merge(Transliteration_Utilities::plugin_default_options(), $options);
		add_option('serbian-transliteration', $options);

		// Set important cookie
		$firstVisitMode = get_rstr_option('first-visit-mode');
		$transliterationMode = get_rstr_option('transliteration-mode');

		if (!isset($_COOKIE['rstr_script'])) {
			if (in_array($firstVisitMode, ['lat', 'cyr'])) {
				Transliteration_Utilities::setcookie($firstVisitMode);
			} else {
				$mode = $transliterationMode === 'cyr_to_lat' ? 'lat' : 'cyr';
				Transliteration_Utilities::setcookie($mode);
			}
		}
		
		// Install database tables
		if (RSTR_DATABASE_VERSION !== get_option('serbian-transliteration-db-version')) {
			Transliteration_Cache_DB::table_install();
			update_option('serbian-transliteration-db-version', RSTR_DATABASE_VERSION, false);
		}

		if( function_exists('flush_rewrite_rules') ) {
			flush_rewrite_rules();
		}

		return true;
	}
	
	
	/*
	 * Register Plugin Deactivation
	 */
	public static function register_deactivation () {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		
		// Unload textdomain
		if (is_textdomain_loaded('serbian-transliteration')) {
			unload_textdomain('serbian-transliteration');
		}
		
		// Reset table check
		delete_option('serbian-transliteration-db-cache-table-exists');
		
		// Delete old translations
		Transliteration_Utilities::clear_plugin_translations();

		// Add deactivation date
		if($deactivation = get_option('serbian-transliteration-deactivation')) {
			$deactivation[] = date('Y-m-d H:i:s');
			update_option('serbian-transliteration-deactivation', $deactivation);
		} else {
			add_option('serbian-transliteration-deactivation', array(date('Y-m-d H:i:s')));
		}

		// Clear plugin cache
		Transliteration_Utilities::clear_plugin_cache();
		
		// Reset permalinks
		if( function_exists('flush_rewrite_rules') ) {
			flush_rewrite_rules();
		}
	}
	
	
	/*
	 * Register Plugin Updater
	 */
	public static function register_updater () {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		
		if ($options['action'] == 'update' && $options['type'] == 'plugin') {
			foreach ($options['plugins'] as $plugin) {
				if ($plugin == plugin_basename(__FILE__)) {
					// Reset table check
					delete_option('serbian-transliteration-db-cache-table-exists');
					
					// Delete old translations
					Transliteration_Utilities::clear_plugin_translations();
					
					// Install database tables
					if (RSTR_DATABASE_VERSION !== get_option('serbian-transliteration-db-version')) {
						Transliteration_Cache_DB::table_install();
						update_option('serbian-transliteration-db-version', RSTR_DATABASE_VERSION, false);
					}
					
					// Clear plugin cache
					Transliteration_Utilities::clear_plugin_cache();
					
					// Reset permalinks
					if( function_exists('flush_rewrite_rules') ) {
						flush_rewrite_rules();
					}
					
					// Save version
					update_option('serbian-transliteration-version', RSTR_VERSION, true);
				}
			}
		}
	}
	
	
	/*
	 * Redirect after activation
	 */
	public static function register_redirection () {
		add_action('activated_plugin', function ($plugin) {
			if( $plugin == RSTR_BASENAME && !get_option('serbian-transliteration-activated')) {
				update_option('serbian-transliteration-activated', true);
				if( wp_safe_redirect( admin_url( 'options-general.php?page=transliteration-settings&rstr-activation=true' ) ) ) {
					exit;
				}
			}
		}, 10, 1);
	}
    
} endif;