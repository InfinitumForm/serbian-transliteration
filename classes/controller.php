<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Controller', false) ) : class Transliteration_Controller {
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		
    }
	
	/*
	 * Transliteration
	 */
	public function transliterate($content, $mode = 'auto') {
		
		if( NULL === $mode || false === $mode ) {
			return $content;
		}
		
		if( $mode == 'auto' ) {
			$mode = get_rstr_option('transliteration-mode', 'cyr_to_lat');
		}
		
		if( method_exists($this, $mode) ) {
			$content = $this->$mode($content);
		}
		
		return $content;
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
	 * Cyrillic to Latin
	 */
	public function cyr_to_lat($content){

		if(Transliteration_Utilities::can_transliterate($content) || Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		$formatSpecifiers = [];
		$content = preg_replace_callback('/(\b\d+(?:\.\d+)?&#37;)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$class_map = Transliteration_Map::get()->map();
		if( class_exists($class_map) ) {
			$content = $class_map::transliterate($content, 'cyr_to_lat');
		//	$content = Transliteration_Sanitization::get()->lat($content);
		}
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}

		return $content;
		
	}
	
	/*
	 * Latin to Cyrillic
	 */
	public function lat_to_cyr($content){

		if(Transliteration_Utilities::can_transliterate($content) || Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		$formatSpecifiers = [];
		$content = preg_replace_callback('/(\b\d+(?:\.\d+)?&#37;)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$class_map = Transliteration_Map::get()->map();
		$content = $class_map::transliterate($content, 'lat_to_cyr');
		$content = Transliteration_Sanitization::get()->cyr($content);
		
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}

		return $content;
		
	}
	
} endif;