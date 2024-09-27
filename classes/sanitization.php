<?php if ( !defined('WPINC') ) die();

final class Transliteration_Sanitization {
	
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
		return apply_filters('transliteration_sanitization_lat', $content, $content, $sanitize_html);
	}
	
	/*
	 * Fix the Cyrillic content
	 */
	public function cyr($content, $sanitize_html = false) {
		return apply_filters('transliteration_sanitization_cyr', $content, $content, $sanitize_html);
	}
	
}