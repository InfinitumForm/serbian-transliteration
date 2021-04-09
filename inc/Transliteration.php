<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Transliterating Mode by locale
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */
if(!class_exists('Serbian_Transliteration_Transliterating')) :
class Serbian_Transliteration_Transliterating {
	
	/*
	 * Registered languages
	 * @since     1.4.3
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function registered_languages(){
		return apply_filters('rstr_registered_languages', array(
			'sr_RS' => __('Serbian', RSTR_NAME),
			'bs_BA' => __('Bosnian', RSTR_NAME),
			'cnr' => __('Montenegrin', RSTR_NAME),
			'ru_RU' => __('Russian', RSTR_NAME),
			'bel' => __('Belarusian', RSTR_NAME),
			'bg_BG' => __('Bulgarian', RSTR_NAME),
			'mk_MK' => __('Macedoanian', RSTR_NAME),
			'kk' => __('Kazakh', RSTR_NAME),
			'uk' => __('Ukrainian', RSTR_NAME),
			'el' => __('Greek', RSTR_NAME)
		));
	}
	
	public function transliteration($content, $translation = 'cyr_to_lat'){
		
		// Avoid transliteration for the some cases
		if(empty($content) || is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)){
			return $content;
		}
		
		// Set variables
		$locale = $this->get_locale();
		$path = RSTR_INC . "/transliteration/{$locale}.php";
		$class_name = "Serbian_Transliteration_{$locale}";
		$transliterated = false;

		// Include class
		if(!class_exists($class_name) && file_exists($path))
		{
			include_once $path;
		}
		
		// Load class
		if(class_exists($class_name))
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
			} if($translation === 'lat_to_cyr') {
				foreach($this->cyr_exclude_list() as $item){
					$content = str_replace($class_name::transliterate($item, 'lat_to_cyr'), $item, $content);
				}
			}
		}
		else
		{
			// Let's do basic transliterationW
			if($translation === 'cyr_to_lat' || $translation === 'lat') {
				$content = str_replace($this->cyr(), $this->lat(), $content);
			} if($translation === 'lat_to_cyr' || $translation === 'cyr') {
				$content = str_replace($this->lat(), $this->cyr(), $content);
			}
			
			// Filter special names from the list
			if($translation === 'cyr_to_lat' || $translation === 'lat') {
				foreach($this->lat_exclude_list() as $item){
					$content = str_replace(str_replace($this->cyr(), $this->lat(), $item), $item, $content);
				}
			} if($translation === 'lat_to_cyr' || $translation === 'cyr') {
				foreach($this->cyr_exclude_list() as $item){
					$content = str_replace(str_replace($this->lat(), $this->cyr(), $item), $item, $content);
				}
			}
		}
		
		return $content;
	}

	/*
	 * Get latin letters in array
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function lat()
	{
		return apply_filters('rstr_lat_letters', array(
			// Variations and special characters
			'nj', 'NJ', 'Nj', 'Lj', 'Dž', 'Dj', 'DJ', 'dj', 'dz', 'JU', 'ju', 'JA', 'ja' ,'ŠČ' ,'šč',
			// Big letters
			'A', 'B', 'V', 'G', 'D', 'Đ', 'E', 'Ž', 'Z', 'I', 'J', 'K', 'L', 'LJ', 'M',
			'N', 'O', 'P', 'R', 'S', 'T', 'Ć', 'U', 'F', 'H', 'C', 'Č', 'DŽ', 'Š',
			// Small letters
			'a', 'b', 'v', 'g', 'd', 'đ', 'e', 'ž', 'z', 'i', 'j', 'k', 'l', 'lj', 'm',
			'n', 'o', 'p', 'r', 's', 't', 'ć', 'u', 'f', 'h', 'c', 'č', 'dž', 'š',
		));
	}
	
	/*
	 * Get cyrillic letters in array
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr()
	{
		return apply_filters('serbian_transliteration_cyr_letters', array(
			// Variations and special characters
			'њ', 'Њ', 'Њ', 'Љ', 'Џ', 'Ђ', 'Ђ', 'ђ', 'ѕ', 'Ю', 'ю', 'Я', 'я' ,'Щ' ,'щ',
			// Big letters
			'А', 'Б', 'В', 'Г', 'Д', 'Ђ', 'Е', 'Ж', 'З', 'И', 'Ј', 'К', 'Л', 'Љ', 'М',
			'Н', 'О', 'П', 'Р', 'С', 'Т', 'Ћ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш',
			// Small letters
			'а', 'б', 'в', 'г', 'д', 'ђ', 'е', 'ж', 'з', 'и', 'ј', 'к', 'л', 'љ', 'м',
			'н', 'о', 'п', 'р', 'с', 'т', 'ћ', 'у', 'ф', 'х', 'ц', 'ч', 'џ', 'ш'			
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
		
		global $rstr_cache;
		if(!$rstr_cache->get('get_locale')){
			$rstr_cache->set('get_locale', get_locale());
		}
        return $rstr_cache->get('get_locale');
	}
	
	/*
	 * Get list of available locales
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public function get_locales( $needle = NULL ){
		$cache = get_transient(RSTR_NAME . '-locales');
		
		if(empty($cache))
		{
			$file_name=apply_filters('rstr/init/libraries/file/locale', 'locale.lib');
			$cache = $this->parse_library($file_name);
			if(!empty($cache)) {
				set_transient(RSTR_NAME . '-locales', $cache, apply_filters('rstr/init/libraries/file/locale/transient', (DAY_IN_SECONDS*7)));
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
	 * THIS IS TEST FUNCTION, NOT FOR THE PRODUCTION
	 * @author        Ivijan-Stefan Stipic
	*/
/*
	private function create_only_diacritical($file, $new_file){
		
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
				
				$words = explode("\n", $buffer);
				$words = array_unique($words);
				$words = array_filter($words);
				$words = array_map('trim', $words);
				
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
	}
*/
	/*
	 * Get list of diacriticals
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public function get_diacritical( $needle = NULL ){
		$cache = get_transient(RSTR_NAME . '-diacritical-words');
		if(empty($cache))
		{
			$file_name=apply_filters('rstr/init/libraries/file/get_diacritical', $this->get_locale().'.diacritical.words.lib');
			$cache = $this->parse_library($file_name);
			if(!empty($cache)) {
				set_transient(RSTR_NAME . '-diacritical-words', $cache, apply_filters('rstr/init/libraries/file/get_diacritical/transient', (DAY_IN_SECONDS*7)));
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
	public function get_skip_words( $needle = NULL ){
		$cache = get_transient(RSTR_NAME . '-skip-words');
		
		if(empty($cache))
		{
			$file_name=apply_filters('rstr/init/libraries/file/skip-words', $this->get_locale().'.skip.words.lib');
			$cache = $this->parse_library($file_name);
			if(!empty($cache)) {
				set_transient(RSTR_NAME . '-skip-words', $cache, apply_filters('rstr/init/libraries/file/skip-words/transient', (DAY_IN_SECONDS*7)));
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
	public function parse_library($file_name, $needle = NULL) {
		
		$words = array();
		$words_file=apply_filters('rstr/init/libraries/file', RSTR_ROOT . '/libraries/' . $file_name);
		
		if(file_exists($words_file))
		{
			if($fopen_locale=fopen($words_file, 'r'))
			{
				$contents = fread($fopen_locale, filesize($words_file));
				fclose($fopen_locale);
				
				if(!empty($contents))
				{
					$words = explode("\n", $contents);
					$words = array_unique($words);
					$words = array_filter($words);
					$words = array_map('trim', $words);
				} else return false;
			} else return false;
		} else return false;
		
		if($needle) {
			return (in_array($needle, $words, true) !== false ? $needle : false);
		} else {
			return $words;
		}
	}
}
endif;