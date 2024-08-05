<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Init', false) ) : class Transliteration_Init extends Transliteration {
    
    public function __construct() {
        // Register the textdomain for the plugin
        $this->add_action('plugins_loaded', 'load_textdomain');
		$this->add_action('template_redirect', 'set_transliteration');
        
        $classes = apply_filters('transliteration_classes_init', [
			'Transliteration_Settings',
			'Transliteration_Controller',
			'Transliteration_Mode',
			'Transliteration_Menus'
		]);
		
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
    
} endif;