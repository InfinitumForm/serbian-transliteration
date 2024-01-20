<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Transliterating Mode by locale
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */
if(!class_exists('Serbian_Transliteration_Transliterating', false)) :
class Serbian_Transliteration_Transliterating {

	/*
	 * Registered languages
	 * @since     1.4.3
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function registered_languages(){
		return apply_filters('rstr_registered_languages', array(
			'sr_RS'  => __('Serbian', 'serbian-transliteration'),
			'bs_BA'  => __('Bosnian', 'serbian-transliteration'),
			'cnr'    => __('Montenegrin', 'serbian-transliteration'),
			'ru_RU'  => __('Russian', 'serbian-transliteration'),
			'bel'    => __('Belarusian', 'serbian-transliteration'),
			'bg_BG'  => __('Bulgarian', 'serbian-transliteration'),
			'mk_MK'  => __('Macedoanian', 'serbian-transliteration'),
			'uk'     => __('Ukrainian', 'serbian-transliteration'),
			'kk'     => __('Kazakh', 'serbian-transliteration'),
			'tg'     => __('Tajik', 'serbian-transliteration'),
			'kir'    => __('Kyrgyz', 'serbian-transliteration'),
			'mn'     => __('Mongolian', 'serbian-transliteration'),
			'ba'     => __('Bashkir', 'serbian-transliteration'),
			'uz_UZ'  => __('Uzbek', 'serbian-transliteration'),
			'ka_GE'  => __('Georgian', 'serbian-transliteration'),
			'el'     => __('Greek', 'serbian-transliteration'),
			'hy'     => __('Armenian', 'serbian-transliteration'),
			'ar'     => __('Arabic', 'serbian-transliteration')
		));
	}
	
	/*
	 * Check if it can be transliterated
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function can_trasliterate($content){
		return apply_filters( 'rstr_can_trasliterate', (
			empty($content) 
			|| is_array($content) 
			|| is_object($content) 
			|| is_numeric($content) 
			|| is_bool($content)
			|| ( is_string($content) && trim($content) != '' && @is_file($content) )
			|| ( is_string($content) && trim($content) != '' && @is_link($content) )
			|| filter_var($content, FILTER_VALIDATE_URL)
			|| filter_var($content, FILTER_VALIDATE_EMAIL)
		), $content );
	}

	/*
	 * Do transliteration
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	 */
	public function transliteration($content, $translation = 'cyr_to_lat'){
		
		if( Serbian_Transliteration_Utilities::exclude_transliteration() ) {
			return $content;
		}
		
		$formatSpecifiers = [];
		$content = preg_replace_callback('/%[0-9]*\$(?:d|s)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);
		
		$content = preg_replace_callback('/%(?:d|s)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$locale = $this->get_locale();
		
		// Avoid transliteration for the some cases
		if( self::can_trasliterate($content) || !in_array($translation, array('lat_to_cyr', 'cyr_to_lat')) ){
			return $content;
		}
		
		$site_script = get_rstr_option('site-script', 'lat');
		$mode = get_rstr_option('transliteration-mode', 'none') == 'cyr_to_lat' ? 'cyr' : 'lat';
		$current = Serbian_Transliteration_Utilities::get_current_script() == 'lat_to_cyr' ? 'cyr' : 'lat';

		if(!is_admin() && ($site_script === $mode) && ($mode === $current) && ($current === 'cyr')){
			return $content;
		}

		// Set variables
		$path = RSTR_INC . "/transliteration/{$locale}.php";
		$class_name = "Serbian_Transliteration_{$locale}";
		$transliterated = false;

		// Include class
		if(!class_exists($class_name, false) && file_exists($path))
		{
			include_once $path;
		}

		// Load class
		if(class_exists($class_name, false))
		{
			$content = $class_name::transliterate($content, $translation);
			$transliterated = true;
		}
		// If no locale than old fashion way
		if($transliterated)
		{
			// Filter special names from the list
			if($translation === 'cyr_to_lat') {
				foreach($this->lat_exclude_list() as $item){
					$content = str_replace($class_name::transliterate($item, 'cyr_to_lat'), $item, $content);
				}
			} else if($translation === 'lat_to_cyr') {
				foreach($this->cyr_exclude_list() as $item){
					$content = str_replace($class_name::transliterate($item, 'lat_to_cyr'), $item, $content);
				}
			}
		}
		else
		{
			// Let's do basic transliteration
			if($translation === 'cyr_to_lat') {
				$content = str_replace(self::cyr(), self::lat(), $content);
				$content = str_replace(array(
					'ь', 'ъ', 'Ъ', 'Ь'
				), '', $content);
			} else if($translation === 'lat_to_cyr') {
				$content = str_replace(self::lat(), self::cyr(), $content);
			}

			// Filter special names from the list
			if($translation === 'cyr_to_lat') {
				foreach($this->lat_exclude_list() as $item){
					$content = str_replace(str_replace(self::cyr(), self::lat(), $item), $item, $content);
				}
			} else if($translation === 'lat_to_cyr') {
				foreach($this->cyr_exclude_list() as $item){
					$content = str_replace(str_replace(self::lat(), self::cyr(), $item), $item, $content);
				}
			}
		}
		
		// Post-transliteracija: Vraćanje format specifikatora
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
			unset($formatSpecifiers);
		}

		return $content;
	}

	/*
	 * Get latin letters in array
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function lat()
	{
		return apply_filters('rstr_lat_letters', array(
			// Variations and special characters
			'nj', 'NJ', 'Nj', 'Lj', 'Dž', 'Dj', 'DJ', 'dj', 'dz', 'JU', 'ju', 'JA', 'ja',
			'ŠČ', 'šč', 'Y', 'y','YO', 'Yo', 'yo', 'YE', 'ye', 'Ǎ', 'ǎ',
			// Big letters
			'A', 'B', 'V', 'G', 'D', 'Đ', 'E', 'Ž', 'Z', 'I', 'J', 'K', 'L', 'LJ', 'M',
			'N', 'O', 'P', 'R', 'S', 'T', 'Ć', 'U', 'F', 'H', 'C', 'Č', 'DŽ', 'Š',
			// Small letters
			'a', 'b', 'v', 'g', 'd', 'đ', 'e', 'ž', 'z', 'i', 'j', 'k', 'l', 'lj', 'm',
			'n', 'o', 'p', 'r', 's', 't', 'ć', 'u', 'f', 'h', 'c', 'č', 'dž', 'š',
			// Specials
			'a', 'b', 'g', 'd', 'e', 'v', 'z', 'th', 'i', 'k', 'l', 'm', 'n', 'o', 'p',
			'zh', 'r', 's', 't', 'u', 'ph', 'q', 'gh', 'qh', 'sh', 'ch', 'ts', 'dz', 'ts',
			'tch', 'kh', 'j', 'h'
		));
	}

	/*
	 * Get cyrillic letters in array
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function cyr()
	{
		return apply_filters('serbian_transliteration_cyr_letters', array(
			// Variations and special characters
			'њ', 'Њ', 'Њ', 'Љ', 'Џ', 'Ђ', 'Ђ', 'ђ', 'ѕ', 'Ю', 'ю', 'Я', 'я',
			'Щ', 'щ', 'Й', 'й', 'Ё', 'Ё', 'ё', 'Э', 'э', 'Ъ', 'ъ',
			// Big letters
			'А', 'Б', 'В', 'Г', 'Д', 'Ђ', 'Е', 'Ж', 'З', 'И', 'Ј', 'К', 'Л', 'Љ', 'М',
			'Н', 'О', 'П', 'Р', 'С', 'Т', 'Ћ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш',
			// Small letters
			'а', 'б', 'в', 'г', 'д', 'ђ', 'е', 'ж', 'з', 'и', 'ј', 'к', 'л', 'љ', 'м',
			'н', 'о', 'п', 'р', 'с', 'т', 'ћ', 'у', 'ф', 'х', 'ц', 'ч', 'џ', 'ш',
			// Specials
			'ა', 'ბ', 'გ', 'დ', 'ე', 'ვ', 'ზ', 'თ', 'ი', 'კ', 'ლ', 'მ', 'ნ', 'ო', 'პ',
			'ჟ', 'რ', 'ს', 'ტ', 'უ', 'ფ', 'ქ', 'ღ', 'ყ', 'შ', 'ჩ', 'ც',
			'ძ', 'წ', 'ჭ', 'ხ', 'ჯ', 'ჰ'
		));
	}


	/*
	 * Get locale
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function get_locale(){

		if('auto' != ($language_scheme = get_rstr_option('language-scheme', 'auto'))) {
			return $language_scheme;
		}

		$get_locale = Serbian_Transliteration_Cache::get('get_locale');

		if(empty($get_locale)){
			$get_locale = get_locale();
			if(function_exists('pll_current_language')) {
				$get_locale = pll_current_language('locale');
			}
			
			if(empty($get_locale)){
				$get_locale = get_user_locale( wp_get_current_user() );
			}
			
			$get_locale = Serbian_Transliteration_Cache::set('get_locale', $get_locale);
		}

		return $get_locale;
	}

	/*
	 * Get list of available locales
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function get_locales( $needle = NULL ){
		$cache = Serbian_Transliteration_DB_Cache::get(RSTR_NAME . '-locales');

		if(empty($cache))
		{
			$file_name=apply_filters('rstr/init/libraries/file/locale', 'locale.lib');
			$cache = self::parse_library($file_name);
			
			if(!empty($cache)) {
				Serbian_Transliteration_DB_Cache::set(RSTR_NAME . '-locales', $cache, apply_filters('rstr/init/libraries/file/locale/transient', YEAR_IN_SECONDS));
			}
		}

		if($needle && is_array($cache)) {
			return (in_array($needle, $cache, true) !== false ? $needle : false);
		}

		return $cache;
	}

	/*
	 * Exclude words or sentences for Cyrillic
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	 * @contributor   Slobodan Pantovic
	*/
	public function cyr_exclude_list(){
		$cyr_exclude_list = apply_filters('rstr/init/exclude/cyr', array());
		$content = ob_get_status() ? ob_get_contents() : false;
		if ( false !== $content ){
			if ( preg_match_all('/\\\u[0-9a-f]{4}/i', $content, $exclude_unicode)){
				$cyr_exclude_list = array_merge($cyr_exclude_list, $exclude_unicode);
			}
		}

		$cyr_exclude_list = array_filter($cyr_exclude_list);

		return $cyr_exclude_list;
	}

	/*
	 * Exclude words or sentences for Latin
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function lat_exclude_list(){
		return apply_filters('rstr/init/exclude/lat', array());
	}

	/*
	 * Create only diacritical library
	 * THIS IS DEVELOPMENT FUNCTION, NOT FOR THE PRODUCTION
	 * @author        Ivijan-Stefan Stipic
	*/
/*	private function create_only_diacritical($file, $new_file){

		if(file_exists($file) || empty($new_file)) return;
		if(preg_match('/(\.lib)/i', $new_file) === false) return;

		$filesize = filesize(RSTR_ROOT.'/libraries/' . $file);
		$fp = @fopen($file, "r");
		$chunk_size = (1<<24); // 16MB arbitrary
		$position = 0;

		$new_file = fopen(RSTR_ROOT.'/libraries/' . $new_file, "w");

		// if handle $fp to file was created, go ahead
		if ($fp)
		{
			while(!feof($fp))
			{
				// move pointer to $position in file
				fseek($fp, $position);

				// take a slice of $chunk_size bytes
				$chunk = fread($fp,$chunk_size);

				// searching the end of last full text line
				$last_lf_pos = strrpos($chunk, "\n");

				// $buffer will contain full lines of text
				// starting from $position to $last_lf_pos
				$buffer = mb_substr($chunk,0,$last_lf_pos);

				$words = Serbian_Transliteration_Utilities::explode("\n", $buffer);
				$words = array_unique($words);

				$save = array();
				foreach($words as $word) {
					if(preg_match('/[čćžšđ]/i', $word)){
						$save[]= $word;
					}
				}
				fwrite($new_file, join("\n", $save)) . "\n";

				// Move $position
				$position += $last_lf_pos;

				// if remaining is less than $chunk_size, make $chunk_size equal remaining
				if(($position+$chunk_size) > $filesize) $chunk_size = ($filesize-$position);
				$buffer = NULL;
			}
			fclose($fp);
			fclose($new_file);
		}
	}*/

	/*
	 * Get list of diacriticals
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function get_diacritical( $needle = NULL ){
		$locale = self::__init()->get_locale();
		$transient_name = RSTR_NAME . "-diacritical-words-{$locale}";
		$cache = Serbian_Transliteration_DB_Cache::get($transient_name);
		if(empty($cache))
		{
			$file_name=apply_filters('rstr/init/libraries/file/get_diacritical', "{$locale}.diacritical.words.lib", $locale, $transient_name);
			$cache = self::parse_library($file_name);
			if(!empty($cache)) {
				Serbian_Transliteration_DB_Cache::set($transient_name, $cache, apply_filters('rstr/init/libraries/file/get_diacritical/transient', (DAY_IN_SECONDS*7)));
			}
		}

		if($needle && is_array($cache)) {
			return (in_array($needle, $cache, true) !== false ? $needle : false);
		}

		return $cache;
	}

	/*
	 * Get skip words
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function get_skip_words( $needle = NULL ){
		$locale = self::__init()->get_locale();
		$transient_name = RSTR_NAME . "-skip-words-{$locale}";
		$cache = Serbian_Transliteration_DB_Cache::get($transient_name);
		if(empty($cache))
		{
			$file_name=apply_filters('rstr/init/libraries/file/skip-words', "{$locale}.skip.words.lib", $locale, $transient_name);
			$cache = self::parse_library($file_name);
			if(!empty($cache)) {
				Serbian_Transliteration_DB_Cache::set($transient_name, $cache, apply_filters('rstr/init/libraries/file/skip-words/transient', (DAY_IN_SECONDS*7)));
			}
		}

		if($needle && is_array($cache)) {
			return (in_array($needle, $cache, true) !== false ? $needle : false);
		}

		return $cache;
	}

	/*
	 * Parse library
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function parse_library($file_name, $needle = NULL) {

		$words = array();
		$words_file=apply_filters('rstr/init/libraries/file', RSTR_ROOT . '/libraries/' . $file_name);

		if(file_exists($words_file))
		{
				$contents = '';
				if($read_file_chunks = self::read_file_chunks($words_file))
				{
					foreach ($read_file_chunks as $chunk) {
						$contents.=$chunk;
					}
				}

				if(!empty($contents))
				{
					$words = Serbian_Transliteration_Utilities::explode("\n", $contents);
					$words = array_unique($words);
				} else return false;
		} else return false;

		if($needle) {
			return (in_array($needle, $words, true) !== false ? $needle : false);
		} else {
			return $words;
		}
	}
	
	/*
	* Read file with chunks with memory free
	* @since     1.6.7
	*/
	private static function read_file_chunks($path) {
		if($handle = fopen($path, "r")) {
			while(!feof($handle)) {
				yield fgets($handle);
			}
			fclose($handle);
		} else {
			return false;
		}
	}
	
	/*
	* Instance
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function __init()
	{
		$class = self::class;
		$instance = Serbian_Transliteration_Cache::get($class);
		if ( !$instance ) {
			$instance = Serbian_Transliteration_Cache::set($class, new self());
		}
		return $instance;
	}
}
endif;
