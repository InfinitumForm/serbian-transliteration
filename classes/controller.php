<?php if ( !defined('WPINC') ) die();

final class Transliteration_Controller extends Transliteration {
	
	/*
	 * The main constructor
	 */
	public function __construct($actions = true) {
		if($actions) {
			$this->add_action('init', 'transliteration_tags_start', 1);
			$this->add_action('shutdown', 'transliteration_tags_end', 100);
		}
    }
	
	/*
	 * Get current instance
	 */
	private static $instance = NULL;
	public static function get() {
		if( NULL === self::$instance ) {
			self::$instance = new self(false);
		}
		return self::$instance;
	}
	
	/*
	 * Transliteration mode
	 */
	public function mode($no_redirect = false) {

		// Get cached mode
		static $mode = NULL;
		
		if (NULL !== $mode) {
			return $mode;
		}
		
		if (Transliteration_Utilities::is_admin()) {
			$mode = 'cyr_to_lat';
			return $mode;
		}
		
		// Settings mode
		$mode = get_rstr_option('transliteration-mode', 'cyr_to_lat');
		
		// Cookie mode
		if (!empty($_COOKIE['rstr_script'] ?? NULL)) {
			switch (sanitize_text_field($_COOKIE['rstr_script'])) {
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
			$url = (Transliteration_Utilities::parse_url())['url'] ?? NULL;
			$redirect = false;

			$has_redirected = get_transient('transliteration_redirect');

			switch (get_rstr_option('first-visit-mode', 'lat')) {
				case 'lat':
					$mode = 'cyr_to_lat';
					Transliteration_Utilities::setcookie('lat');
					$redirect = true;
					break;
				
				case 'cyr':
					$mode = 'lat_to_cyr';
					Transliteration_Utilities::setcookie('cyr');
					$redirect = true;
					break;
			}

			if (!$has_redirected && !$no_redirect && $redirect && $url && !is_admin() && !headers_sent() && function_exists('wp_safe_redirect')) {
				set_transient('transliteration_redirect', true, 30);
				if ($url && wp_safe_redirect($url, 301)) {
					exit;
				}
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
			if( Transliteration_Utilities::is_admin() ) {
				$lat_exclude_list = [];
			} else {
				$lat_exclude_list = apply_filters('rstr/init/exclude/lat', []);
			}
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
			if( Transliteration_Utilities::is_admin() ) {
				$cyr_exclude_list = [];
			} else {
				$cyr_exclude_list = apply_filters('rstr/init/exclude/cyr', []);
			}
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
		
		if( $this->disable_transliteration() && !Transliteration_Utilities::is_admin() ) {
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
		if( $this->disable_transliteration() && Transliteration_Utilities::is_admin() ) {
			return $content;
		}
		
		return $this->transliterate($content, $mode, false);
	}
	
	/*
	 * Transliteration of attributes
	 */
	public function transliterate_attributes(array $attr, array $keys, $mode = 'auto') {
		
		foreach ($keys as $key) {
			$key = sanitize_key($key);
			if (isset($attr[$key])) {
				$attr[$key] = esc_attr($this->transliterate($attr[$key], $mode));
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
		
		// Retrieve the list of Cyrillic words to exclude from transliteration
		$exclude_list = $this->cyr_exclude_list();
		$exclude_placeholders = [];

		// Check if the exclusion list is not empty
		if (!empty($exclude_list)) {
			foreach ($exclude_list as $key => $word) {
				$placeholder = '@=[0-' . $key . ']=@';
				$content = str_replace($word, $placeholder, $content);
				$exclude_placeholders[$placeholder] = $word;
			}
		}
		
		// Extract <script> contents and replace them with placeholders
		$script_placeholders = [];
		$content = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function($matches) use (&$script_placeholders) {
			$placeholder = '@=[1-' . count($script_placeholders) . ']=@';
			$script_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Extract <style> contents and replace them with placeholders
		$style_placeholders = [];
		$content = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function($matches) use (&$style_placeholders) {
			$placeholder = '@=[2-' . count($style_placeholders) . ']=@';
			$style_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Handle percentage format specifiers by replacing them with placeholders
		$formatSpecifiers = [];
		$content = preg_replace_callback('/(\b\d+(?:\.\d+)?&#37;)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[3-' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Perform the transliteration using the class map
		if (class_exists($class_map)) {
			$content = $class_map::transliterate($content, 'cyr_to_lat');
			$content = Transliteration_Sanitization::get()->lat($content, $sanitize_html);
		}

		// Restore percentage format specifiers back to their original form
		if ($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
			unset($formatSpecifiers);
		}

		// Sanitize HTML attributes and transliterate their values
		if ($sanitize_html) {
			$html_attributes_match = $this->private__html_atributes('cyr_to_lat');
			if($html_attributes_match) {
				$content = preg_replace_callback('/\b('.$html_attributes_match.')=("|\')(.*?)\2/i', function($matches) use ($class_map, $sanitize_html) {
					$transliteratedValue = $class_map::transliterate($matches[3], 'cyr_to_lat');
					$transliteratedValue = Transliteration_Sanitization::get()->lat($transliteratedValue, $sanitize_html);
					return $matches[1] . '=' . $matches[2] . esc_attr($transliteratedValue) . $matches[2];
				}, $content);
			}
		}

		// Restore excluded words back to their original form
		if ($exclude_placeholders) {
			$content = strtr($content, $exclude_placeholders);
			unset($exclude_placeholders);
		}
		
		// Restore <script> contents back to their original form
		if ($script_placeholders) {
			$content = strtr($content, $script_placeholders);
			unset($script_placeholders);
		}

		// Restore <style> contents back to their original form
		if ($style_placeholders) {
			$content = strtr($content, $style_placeholders);
			unset($style_placeholders);
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

		// Decode content if necessary
		$content = Transliteration_Utilities::decode($content);

		// Transliterate from Cyrillic to Latin
		$content = $this->cyr_to_lat($content, false);
		
		// Normalize the string
		$content = Transliteration_Utilities::normalize_latin_string($content);

		// Perform sanitization based on available functions
		if (function_exists('mb_convert_encoding')) {
			// Use mb_convert_encoding if available
			$content = mb_convert_encoding($content, 'ASCII', 'UTF-8');
		} elseif (function_exists('iconv')) {
			// Fallback to iconv if mb_convert_encoding is not available
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
		
		// Retrieve the list of Latin words to exclude from transliteration
		$exclude_list = $this->lat_exclude_list();
		$exclude_placeholders = [];
		if (!empty($exclude_list)) {
			foreach ($exclude_list as $key => $word) {
				$placeholder = '@=[0-' . $key . ']=@';
				$content = str_replace($word, $placeholder, $content);
				$exclude_placeholders[$placeholder] = $word;
			}
		}
		
		// Extract shortcode contents and replace them with placeholders
		$shortcode_placeholders = [];
		$content = preg_replace_callback('/\[([\w+_-]+)\s?([^\]]*)\](.*?)\[\/\1\]/is', function($matches) use (&$shortcode_placeholders) {
			$placeholder = '@=[1-' . count($shortcode_placeholders) . ']=@';
			$shortcode_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);
		
		// Extract self-closing shortcode contents and replace them with placeholders
		$self_closing_shortcode_placeholders = [];
		$content = preg_replace_callback('/\[(\w+)([^\]]*)\]/is', function($matches) use (&$self_closing_shortcode_placeholders) {
			$placeholder = '@=[2-' . count($self_closing_shortcode_placeholders) . ']=@';
			$self_closing_shortcode_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);
		
		// Extract <script> contents and replace them with placeholders
		$script_placeholders = [];
		$content = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function($matches) use (&$script_placeholders) {
			$placeholder = '@=[3-' . count($script_placeholders) . ']=@';
			$script_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Extract <style> contents and replace them with placeholders
		$style_placeholders = [];
		$content = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function($matches) use (&$style_placeholders) {
			$placeholder = '@=[4-' . count($style_placeholders) . ']=@';
			$style_placeholders[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);
		
		// Extract special shortcode contents and replace them with placeholders
		$special_shortcodes = [];
		$content = preg_replace_callback('/\{\{([\w+_-]+)\s?([^\}]*)\}\}(.*?)\{\{\/\1\}\}/is', function($matches) use (&$special_shortcodes) {
			$placeholder = '@=[5-' . count($special_shortcodes) . ']=@';
			$special_shortcodes[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$content = preg_replace_callback('/\{([\w+_-]+)\s?([^\}]*)\}(.*?)\{\/\1\}/is', function($matches) use (&$special_shortcodes) {
			$placeholder = '@=[6-' . count($special_shortcodes) . ']=@';
			$special_shortcodes[$placeholder] = $matches[0];
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
			$placeholder = '@=[7-' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		// Perform the transliteration using the class map
		$content = $class_map::transliterate($content, 'lat_to_cyr');
		$content = Transliteration_Sanitization::get()->cyr($content, $sanitize_html);

		// Restore format specifiers back to their original form
		if ($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
			unset($formatSpecifiers);
		}
		
		// Fix the diacritics
		if($fix_diacritics) {
			$content = $this->fix_diacritics($content);
		}
		
		// Restore self-closing shortcode contents back to their original form
		if ($self_closing_shortcode_placeholders) {
			$content = strtr($content, $self_closing_shortcode_placeholders);
			unset($self_closing_shortcode_placeholders);
		}
		
		// Restore special shortcode contents back to their original form
		if ($special_shortcodes) {
			$content = strtr($content, $special_shortcodes);
			unset($special_shortcodes);
		}
		
		// Restore shortcode contents back to their original form
		if ($shortcode_placeholders) {
			$content = strtr($content, $shortcode_placeholders);
			unset($shortcode_placeholders);
		}
		
		// Sanitize HTML attributes and transliterate their values
		if ($sanitize_html) {
			$html_attributes_match = $this->private__html_atributes('lat_to_cyr');
			if($html_attributes_match) {
				$content = preg_replace_callback('/\b('.$html_attributes_match.')=("|\')(.*?)\2/i', function($matches) use ($class_map, $sanitize_html, $fix_diacritics) {
					$transliteratedValue = $class_map::transliterate($matches[3], 'lat_to_cyr');
					$transliteratedValue = Transliteration_Sanitization::get()->cyr($transliteratedValue, $sanitize_html);
					if($fix_diacritics) {
						$transliteratedValue = $this->fix_diacritics($transliteratedValue);
					}
					return $matches[1] . '=' . $matches[2] . esc_attr($transliteratedValue) . $matches[2];
				}, $content);
			}
		}

		// Restore excluded words back to their original form
		if ($exclude_placeholders) {
			$content = strtr($content, $exclude_placeholders);
			unset($exclude_placeholders);
		}
		
		// Restore <script> contents back to their original form
		if ($script_placeholders) {
			$content = strtr($content, $script_placeholders);
			unset($script_placeholders);
		}

		// Restore <style> contents back to their original form
		if ($style_placeholders) {
			$content = strtr($content, $style_placeholders);
			unset($style_placeholders);
		}

		return $content;
	}
	
	/*
	 * Transliteration tags buffer start
	 */
	public function transliteration_tags_start() {
		$this->ob_start('transliteration_tags_callback');
	}
	
	/*
	 * Transliteration tags buffer callback
	 */
	public function transliteration_tags_callback($buffer) {
		if( Transliteration_Utilities::can_transliterate($buffer) || Transliteration_Utilities::is_editor() ) {
			return $buffer;
		}
		
		if( get_rstr_option('transliteration-mode', 'cyr_to_lat') === 'none' ) {
			return str_replace(
				['{cyr_to_lat}', '{lat_to_cyr}', '{rstr_skip}', '{/cyr_to_lat}', '{/lat_to_cyr}', '{/rstr_skip}'],
				'',
				$buffer
			);
		}
		
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
	public function transliteration_tags_end() {
		if (ob_get_level() > 0) {
			ob_end_flush();
		}
	}
	
	/*
	 * Fix the diactritics
	 */
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
		
		if (count($arr) !== count($arr_origin)) {
			return $content;
		}

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

			$result .= ' ';
		}
		
		$result = trim($result);

		return $result ?: $content;
	}
	
	/*
	 * PRIVATE: Allowed HTML attributes for transliteration
	 */
	private function private__html_atributes($type = 'inherit') {
		static $html_attributes_match = [];
		
		if(!array_key_exists($type, $html_attributes_match)) {
			$html_attributes_match[$type]= apply_filters('transliteration_html_attributes', [
				'title',
				'data-title',
				'alt',
				'placeholder',
				'data-placeholder',
				'aria-label',
				'data-label'
			], $type);
			
			$html_attributes_match[$type] =  is_array($html_attributes_match[$type]) ? array_map('trim', $html_attributes_match[$type]) : [];
			$html_attributes_match[$type] = join('|', $html_attributes_match[$type]);
		}
		
		return $html_attributes_match[$type];
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
	
}