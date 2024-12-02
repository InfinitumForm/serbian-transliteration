<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Woocommerce {
	use Transliteration__Cache;
    
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
	public static function get() {
		return self::cached_static('instance', function(){
			return new self();
		});
	}
	
	/*
	 * Get available filters for this mode
	 */
	public function filters() {
		return [];
	}
    
}