<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Init', false) ) : class Transliteration_Init extends Transliteration {
    
    public function __construct() {
        // Register the textdomain for the plugin
        $this->add_action('plugins_loaded', 'load_textdomain');
        
        new Transliteration_Settings();
        new Transliteration_Controller();
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
		$domain_path = RSTR_ROOT . DIRECTORY_SEPARATOR . 'languages';
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