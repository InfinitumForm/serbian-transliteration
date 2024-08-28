<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Woocommerce {
    
	// Mode ID
	const MODE = 'woocommerce';
	
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
	 * Get available filters for this mode
	 */
	public function filters() {
		return [];
	}
    
}