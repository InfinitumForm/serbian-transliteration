<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Sanitization', false) ) : class Transliteration_Sanitization {
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		
    }
	
	/*
	 * Get current instance
	 */
	private static $instance = null;
	public static function get() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	/*
	 * Fix the Latin content
	 */
	public function lat($content, $sanitize_html = false) {
		return $content;
	}
	
	/*
	 * Fix the Cyrillic content
	 */
	public function cyr($content, $sanitize_html = false) {
		return $content;
	}
	
} endif;