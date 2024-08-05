<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Controller', false) ) : class Transliteration_Controller extends Transliteration {
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		$this->add_action('template_redirect', 'transliteration_tags_start');	
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
	 * Transliteration mode
	 */
	public function mode() {
		
		// Get cached mode
		static $mode = NULL;
		if( $mode ) {
			return $mode;
		}
		
		// Settings mode
		$mode = get_rstr_option('transliteration-mode', 'cyr_to_lat');
		
		// Cookie mode
		if( !empty( $_COOKIE['rstr_script'] ?? NULL ) ){
			switch( trim($_COOKIE['rstr_script']) ) {
				case 'lat':
					$mode = 'cyr_to_lat';
					break;
				
				case 'cyr':
					$mode = 'lat_to_cyr';
					break;
			}
		}
		
		// First visit mode
		else {			
			switch( get_rstr_option('first-visit-mode', 'lat') ) {
				case 'lat':
					$mode = 'cyr_to_lat';
					Transliteration_Utilities::setcookie('lat');
					break;
				
				case 'cyr':
					$mode = 'lat_to_cyr';
					Transliteration_Utilities::setcookie('cyr');
					break;
			}
		}
		
		return $mode;
	}
	
	/*
	 * Transliteration
	 */
	public function transliterate($content, $mode = 'auto', $sanitize_html = true) {
		
		if( NULL === $mode || false === $mode ) {
			return $content;
		}
		
		if( $mode == 'auto' ) {
			$mode = $this->mode();
		}
		
		if( method_exists($this, $mode) ) {
			$content = $this->$mode($content, $sanitize_html);
		}
		
		return $content;
	}
	
	/*
	 * Transliteration with no HTML
	 */
	public function transliterate_no_html($content, $mode = 'auto') {
		return $this->transliterate($content, $mode, false);
	}
	
	/*
	 * Transliteration of attributes
	 */
	public function transliterate_attributes(array $attr, array $keys, $mode = 'auto') {
		
		foreach ($keys as $key) {
			if (isset($attr[$key])) {
				$attr[$key] = esc_attr($this->transliterate_no_html($attr[$key]), $mode);
			}
		}
		
		return $attr;
	}
	
	/*
	 * Cyrillic to Latin
	 */
	public function cyr_to_lat($content, $sanitize_html = true){

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
			$content = Transliteration_Sanitization::get()->lat($content, $sanitize_html);
		}
		
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}
		
		if($sanitize_html) {
			$content = preg_replace_callback('/\b(title|data-title|alt|placeholder|data-placeholder)=("|\')(.*?)\2/i', function($matches) use ($class_map, $sanitize_html) {
				$transliteratedValue = $class_map::transliterate($matches[3], 'cyr_to_lat');
				$transliteratedValue = Transliteration_Sanitization::get()->cyr($transliteratedValue, $sanitize_html);
				return $matches[1] . '=' . $matches[2] . esc_attr($transliteratedValue) . $matches[2];
			}, $content);
		}

		return $content;
		
	}
	
	/*
	 * Translate from cyr to lat
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_to_lat_sanitize($content) {
		if (Transliteration_Utilities::can_transliterate($content) || Transliteration_Utilities::is_editor()) {
			return $content;
		}

		// Transliterate from Cyrillic to Latin
		$content = $this->cyr_to_lat($content, false);
		
		// Normalize the string
		$content = Transliteration_Utilities::normalize_latin_string($content);

		// If iconv is available, perform additional sanitization
		if (function_exists('iconv')) {
			$locale = Transliteration_Utilities::get_locales(Transliteration_Utilities::get_locale());
			if ($locale && preg_match('/^[a-zA-Z]{2}(_[a-zA-Z]{2})?$/', $locale)) {
				// Save the current locale to restore it later
				$current_locale = setlocale(LC_CTYPE, 0);
				
				// Set the new locale
				setlocale(LC_CTYPE, $locale);
				
				// Perform the conversion
				$converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $content);
				
				// Restore the original locale
				setlocale(LC_CTYPE, $current_locale);

				if ($converted) {
					$content = str_replace(array("\"", "'", "`", "^", "~"), '', $converted);
				}
			}
		}

		return $content;
	}
	
	/*
	 * Latin to Cyrillic
	 */
	public function lat_to_cyr($content, $sanitize_html = true){

		if(Transliteration_Utilities::can_transliterate($content) || Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		$formatSpecifiers = [];
		$regex = (
			$sanitize_html
			? '/(\b\d+(?:\.\d+)?&#37;|%\d*\$?[ds]|<[^>]+>|&[^;]+;|https?:\/\/[^\s]+|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}|cyr_to_lat|lat_to_cyr)/'
			: '/(\b\d+(?:\.\d+)?&#37;|%\d*\$?[ds]|&[^;]+;|https?:\/\/[^\s]+|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}|cyr_to_lat|lat_to_cyr)/');
		$content = preg_replace_callback($regex, function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$class_map = Transliteration_Map::get()->map();
		$content = $class_map::transliterate($content, 'lat_to_cyr');
		$content = Transliteration_Sanitization::get()->cyr($content, $sanitize_html);
		
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}
		
		if($sanitize_html) {
			$content = preg_replace_callback('/\b(title|data-title|alt|placeholder|data-placeholder)=("|\')(.*?)\2/i', function($matches) use ($class_map, $sanitize_html) {
				$transliteratedValue = $class_map::transliterate($matches[3], 'lat_to_cyr');
				$transliteratedValue = Transliteration_Sanitization::get()->cyr($transliteratedValue, $sanitize_html);
				return $matches[1] . '=' . $matches[2] . esc_attr($transliteratedValue) . $matches[2];
			}, $content);
		}

		return $content;
	}
	
	/*
	 * Transliteration tags buffer start
	 */
	function transliteration_tags_start() {
		$this->ob_start('transliteration_tags_callback');
		$this->add_action('shutdown', 'transliteration_tags_end', PHP_INT_MAX - 100);
	}
	
	/*
	 * Transliteration tags buffer callback
	 */
	function transliteration_tags_callback($buffer) {
		
		$tags = [
			'cyr_to_lat',
			'lat_to_cyr'
		];
		foreach($tags as $tag) {
			preg_match_all('/\{'.$tag.'\}((?:[^\{\}]|(?R))*)\{\/'.$tag.'\}/s', $buffer, $match, PREG_SET_ORDER);
			foreach ($match as $match) {
				$original_text = $match[1];
				$transliterated_text = $this->transliterate($original_text, $tag, true);
				$buffer = str_replace($match[0], $transliterated_text, $buffer);
			}
		}
		
		return $buffer;
	}

	/*
	 * Transliteration tags buffer end
	 */
	function transliteration_tags_end() {
		if (ob_get_level() > 0) {
			ob_end_flush();
		}
	}
	
} endif;