<?php if ( !defined('WPINC') ) die();

class Transliteration_Map {
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		
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
	 * Get the current language map
	 */
	public function map() {
		static $current_map = NULL;

		if (NULL === $current_map) {
			// Fetch language scheme and disabled languages
			$language_scheme = get_rstr_option('language-scheme', 'auto');
			$disable_languages = get_rstr_option('disable-by-language', []);

			// Filter disabled languages
			if (is_array($disable_languages)) {
				$disable_languages = array_keys(array_filter($disable_languages, function($value) {
					return $value == 'yes';
				}));
			} else {
				$disable_languages = [];
			}

			// Determine the language scheme
			if ($language_scheme === 'auto') {
				$language_scheme = Transliteration_Utilities::get_locale();
			}

			// Check if the language scheme is disabled
			if ($disable_languages && in_array($language_scheme, $disable_languages)) {
				return NULL;
			}

			// Dynamically load the class for the language scheme
			$class = 'Transliteration_Map_' . $language_scheme;
			if (class_exists($class)) {
				$current_map = $class;
			}
		}

		return $current_map;
	}

	
}