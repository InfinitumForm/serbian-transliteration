<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Map', false) ) : class Transliteration_Map {
	
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
	public function map () {
		static $current_map = NULL;
		
		if( NULL === $current_map ) {
			$language_scheme = get_rstr_option('language-scheme', 'auto');
			
			if( $language_scheme === 'auto' ) {
				$language_scheme = Transliteration_Utilities::get_locale();
			}
			
			$class = 'Transliteration_Map_' . $language_scheme;
			
			if( class_exists($class) ) {
				$current_map = $class;
			}
		}
		
		return $current_map;
	}
	
} endif;