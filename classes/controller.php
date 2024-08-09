<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Controller', false) ) : class Transliteration_Controller extends Transliteration {
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		$this->add_action('init', 'transliteration_tags_start', 1);
		$this->add_action('shutdown', 'transliteration_tags_end', 100);
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
		
		if( is_admin() && !wp_doing_ajax() ) {
			$mode = 'cyr_to_lat';
			return $mode;
		}
		
		// Settings mode
		$mode = get_rstr_option('transliteration-mode', 'cyr_to_lat');
		
		// Cookie mode
		if( !empty( $_COOKIE['rstr_script'] ?? NULL ) ){
			switch( sanitize_text_field($_COOKIE['rstr_script']) ) {
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
	
	/**
	 * Exclude words or sentences for Latin
	 * @return        array
	 * @autor        Ivijan-Stefan Stipic
	 */
	public function lat_exclude_list() {
		static $lat_exclude_list = null;
		if ($lat_exclude_list === null) {
			$lat_exclude_list = apply_filters('rstr/init/exclude/lat', []);
		}
		return $lat_exclude_list;
	}

	/**
	 * Exclude words or sentences for Cyrillic
	 * @return        array
	 * @autor        Ivijan-Stefan Stipic
	 */
	public function cyr_exclude_list() {
		static $cyr_exclude_list = null;
		if ($cyr_exclude_list === null) {
			$cyr_exclude_list = apply_filters('rstr/init/exclude/cyr', []);
		}
		return $cyr_exclude_list;
	}
	
	/*
	 * Disable Transliteration for the same script
	 */
	public function disable_transliteration() {
		static $disable_transliteration = NULL;
		
		if (NULL !== $disable_transliteration) {
			return $disable_transliteration;
		}
		
		$current_script = get_rstr_option('site-script', 'cyr');
		$transliteration_mode = get_rstr_option('transliteration-mode', 'cyr_to_lat');
		$current_mode = sanitize_text_field($_COOKIE['rstr_script'] ?? '');

		$disable_transliteration = false;
		
		if (($current_script == 'cyr' && $transliteration_mode == 'cyr_to_lat' && $current_mode == 'cyr') ||
			($current_script == 'lat' && $transliteration_mode == 'lat_to_cyr' && $current_mode == 'lat')) {
			$disable_transliteration = true;
		}
		
		return $disable_transliteration;
	}

	
	/*
	 * Transliteration
	 */
	public function transliterate($content, $mode = 'auto', $sanitize_html = true) {
		
		if( $this->disable_transliteration() && !is_admin() && !wp_doing_ajax() ) {
			return $content;
		}
		
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
		if( $this->disable_transliteration() && !is_admin() && !wp_doing_ajax() ) {
			return $content;
		}
		
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
	public function cyr_to_lat($content, bool $sanitize_html = true) {
		$class_map = Transliteration_Map::get()->map();
		
		// If the content should not be transliterated or the user is an editor, return the original content
		if (!$class_map || Transliteration_Utilities::can_transliterate($content) || Transliteration_Utilities::is_editor()) {
			return $content;
		}
		
		/*// Don't transliterate if we already have transliteration
		if (!Transliteration_Utilities::is_lat($content)) {
			return $content;
		}*/
		
		// Extract <script> contents and replace them with placeholders
		$script_placeholders = [];
		$content = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function($matches) use (&$script_placeholders) {
			$placeholder = '@script' . count($script_placeholders) . '@';
			$script_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Extract <style> contents and replace them with placeholders
		$style_placeholders = [];
		$content = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function($matches) use (&$style_placeholders) {
			$placeholder = '@style' . count($style_placeholders) . '@';
			$style_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Handle percentage format specifiers by replacing them with placeholders
		$formatSpecifiers = [];
		$content = preg_replace_callback('/(\b\d+(?:\.\d+)?&#37;)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Retrieve the list of Cyrillic words to exclude from transliteration
		$exclude_list = $this->cyr_exclude_list();
		$exclude_placeholders = [];

		// Check if the exclusion list is not empty
		if (!empty($exclude_list)) {
			foreach ($exclude_list as $key => $word) {
				$placeholder = '@#=' . $key . '=#@';
				$content = str_replace($word, $placeholder, $content);
				$exclude_placeholders[$placeholder] = $word;
			}
		}

		// Perform the transliteration using the class map
		if (class_exists($class_map)) {
			$content = $class_map::transliterate($content, 'cyr_to_lat');
			$content = Transliteration_Sanitization::get()->lat($content, $sanitize_html);
		}

		// Restore percentage format specifiers back to their original form
		if ($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}

		// Sanitize HTML attributes and transliterate their values
		if ($sanitize_html) {
			$content = preg_replace_callback('/\b(title|data-title|alt|placeholder|data-placeholder|aria-label|data-label)=("|\')(.*?)\2/i', function($matches) use ($class_map, $sanitize_html) {
				$transliteratedValue = $class_map::transliterate($matches[3], 'cyr_to_lat');
				$transliteratedValue = Transliteration_Sanitization::get()->cyr($transliteratedValue, $sanitize_html);
				return $matches[1] . '=' . $matches[2] . esc_attr($transliteratedValue) . $matches[2];
			}, $content);
		}

		// Restore excluded words back to their original form
		if ($exclude_placeholders) {
			$content = strtr($content, $exclude_placeholders);
		}
		
		// Restore <script> contents back to their original form
		if ($script_placeholders) {
			$content = strtr($content, $script_placeholders);
		}

		// Restore <style> contents back to their original form
		if ($style_placeholders) {
			$content = strtr($content, $style_placeholders);
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
		
		/*// Don't transliterate if we already have transliteration
		if (!Transliteration_Utilities::is_lat($content)) {
			return $content;
		}*/
		
		$content = Transliteration_Utilities::decode($content);

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
	public function lat_to_cyr($content, bool $sanitize_html = true, bool $fix_diacritics = false) {
		$class_map = Transliteration_Map::get()->map();
		
		// If the content should not be transliterated or the user is an editor, return the original content
		if (!$class_map || Transliteration_Utilities::can_transliterate($content) || Transliteration_Utilities::is_editor()) {
			return $content;
		}
		
		/*// Don't transliterate if we already have transliteration
		if (!Transliteration_Utilities::is_cyr($content)) {
			return $content;
		}*/
		
		// Extract <script> contents and replace them with placeholders
		$script_placeholders = [];
		$content = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function($matches) use (&$script_placeholders) {
			$placeholder = '@script' . count($script_placeholders) . '@';
			$script_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Extract <style> contents and replace them with placeholders
		$style_placeholders = [];
		$content = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function($matches) use (&$style_placeholders) {
			$placeholder = '@style' . count($style_placeholders) . '@';
			$style_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Handle various format specifiers and other patterns by replacing them with placeholders
		$formatSpecifiers = [];
		$regex = (
			$sanitize_html
			? '/(\b\d+(?:\.\d+)?&#37;|%\d*\$?[ds]|<[^>]+>|&[^;]+;|https?:\/\/[^\s]+|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}|rstr_skip|cyr_to_lat|lat_to_cyr)/'
			: '/(\b\d+(?:\.\d+)?&#37;|%\d*\$?[ds]|&[^;]+;|https?:\/\/[^\s]+|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}|rstr_skip|cyr_to_lat|lat_to_cyr)/'
		);
		$content = preg_replace_callback($regex, function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Retrieve the list of Latin words to exclude from transliteration
		$exclude_list = $this->lat_exclude_list();
		$exclude_placeholders = [];
		if (!empty($exclude_list)) {
			foreach ($exclude_list as $key => $word) {
				$placeholder = '@#=' . $key . '=#@';
				$content = str_replace($word, $placeholder, $content);
				$exclude_placeholders[$placeholder] = $word;
			}
		}

		// Perform the transliteration using the class map
		$content = $class_map::transliterate($content, 'lat_to_cyr');
		$content = Transliteration_Sanitization::get()->cyr($content, $sanitize_html);

		// Restore format specifiers back to their original form
		if ($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}

		// Sanitize HTML attributes and transliterate their values
		if ($sanitize_html) {
			$content = preg_replace_callback('/\b(title|data-title|alt|placeholder|data-placeholder|aria-label|data-label)=("|\')(.*?)\2/i', function($matches) use ($class_map, $sanitize_html, $fix_diacritics) {
				$transliteratedValue = $class_map::transliterate($matches[3], 'lat_to_cyr');
				$transliteratedValue = Transliteration_Sanitization::get()->cyr($transliteratedValue, $sanitize_html);
				if($fix_diacritics) {
					$transliteratedValue = $this->fix_diacritics($transliteratedValue);
				}
				return $matches[1] . '=' . $matches[2] . esc_attr($transliteratedValue) . $matches[2];
			}, $content);
		}
		
		// Fix the diacritics
		if($fix_diacritics) {
			$content = $this->fix_diacritics($content);
		}

		// Restore excluded words back to their original form
		if ($exclude_placeholders) {
			$content = strtr($content, $exclude_placeholders);
		}
		
		// Restore <script> contents back to their original form
		if ($script_placeholders) {
			$content = strtr($content, $script_placeholders);
		}

		// Restore <style> contents back to their original form
		if ($style_placeholders) {
			$content = strtr($content, $style_placeholders);
		}

		return $content;
	}

	
	/*
	 * Transliteration tags buffer start
	 */
	function transliteration_tags_start() {
		$this->ob_start('transliteration_tags_callback');
	}
	
	/*
	 * Transliteration tags buffer callback
	 */
	function transliteration_tags_callback($buffer) {
		
		$tags = [
			'cyr_to_lat',
			'lat_to_cyr',
			'rstr_skip'
		];
		foreach($tags as $tag) {
			preg_match_all('/\{'.$tag.'\}((?:[^\{\}]|(?R))*)\{\/'.$tag.'\}/s', $buffer, $match, PREG_SET_ORDER);
			foreach ($match as $match) {
				$original_text = $match[1];
				if( $tag === 'rstr_skip' ) {
					$mode = ($this->mode() == 'cyr_to_lat' ? 'lat_to_cyr' : 'cyr_to_lat');
					$transliterated_text = $this->$mode($original_text, true);
				} else {
					$transliterated_text = $this->$tag($original_text, true);
				}
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
	
	public static function fix_diacritics($content) {
		if ( Transliteration_Utilities::can_transliterate($content) || Transliteration_Utilities::is_editor() || 
			!in_array(Transliteration_Utilities::get_locale(), ['sr_RS', 'bs_BA', 'cnr'])) {
			return $content;
		}

		$search = Transliteration_Utilities::get_diacritical();
		if (!$search) {
			return $content;
		}

		$new_string = strtr($content, [
			'dj' => 'đ', 'Dj' => 'Đ', 'DJ' => 'Đ',
			'sh' => 'š', 'Sh' => 'Š', 'SH' => 'Š',
			'ch' => 'č', 'Ch' => 'Č', 'CH' => 'Č',
			'cs' => 'ć', 'Cs' => 'Ć', 'CS' => 'Ć',
			'dz' => 'dž', 'Dz' => 'Dž', 'DZ' => 'DŽ'
		]);

		$skip_words = array_map('strtolower', Transliteration_Utilities::get_skip_words());
		$search = array_map('strtolower', $search);

		$arr = Transliteration_Utilities::explode(' ', $new_string);
		$arr_origin = Transliteration_Utilities::explode(' ', $content);

		$result = '';
		foreach ($arr as $i => $word) {
			$word_origin = $arr_origin[$i];
			$word_search = strtolower(preg_replace('/[.,?!-*_#$]+/i', '', $word));
			$word_search_origin = strtolower(preg_replace('/[.,?!-*_#$]+/i', '', $word_origin));

			if (in_array($word_search_origin, $skip_words)) {
				$result .= $word_origin . ' ';
				continue;
			}

			if (in_array($word_search, $search)) {
				$result .= self::apply_case($word, $search, $word_search);
			} else {
				$result .= $word;
			}

			$result .= ($i < count($arr) - 1) ? ' ' : '';
		}

		return $result ?: $content;
	}

	/*
	 * PRIVATE: Apply Case
	 */
	private static function apply_case($word, $search, $word_search) {
		if (ctype_upper($word) || preg_match('~^[A-ZŠĐČĆŽ]+$~u', $word)) {
			return strtoupper($search[array_search($word_search, $search)]);
		} elseif (preg_match('~^\p{Lu}~u', $word)) {
			$ucfirst = $search[array_search($word_search, $search)];
			return strtoupper(substr($ucfirst??'', 0, 1)) . substr($ucfirst??'', 1);
		}
		return $word;
	}
	
} endif;