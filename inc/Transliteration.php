<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Transliterating Mode by locale
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Transliterating')) :
class Serbian_Transliteration_Transliterating {
	// Define locale instance
	protected $get_locale;

	/*
	 * Serbian transliteration
	 * @since     1.0.2
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function sr_RS ($content, $translation = 'cyr_to_lat')
	{
		$transliteration = apply_filters('rstr/inc/transliteration/sr_RS', array(
			// Variations and special characters
			'џ'=>'dž',	'Џ'=>'Dž',	'љ'=>'lj',	'Љ'=>'Lj',
			'њ'=>'nj', 'Њ'=>'Nj',
			
			// All other letters
			'А'=>'A',	'Б'=>'B',	'В'=>'V',	'Г'=>'G',	'Д'=>'D', 
			'Ђ'=>'Đ',	'Е'=>'E',	'Ж'=>'Ž',	'З'=>'Z',	'И'=>'I',
			'Ј'=>'J',	'К'=>'K',	'Л'=>'L',	'М'=>'M',	'Н'=>'N',
			'О'=>'O',	'П'=>'P',	'Р'=>'R',	'С'=>'S',	'Ш'=>'Š',
			'Т'=>'T',	'Ћ'=>'Ć',	'У'=>'U',	'Ф'=>'F',	'Х'=>'H',
			'Ц'=>'C',	'Ч'=>'Č',	'а'=>'a',	'б'=>'b',	'в'=>'v',
			'г'=>'g',	'д'=>'d',	'ђ'=>'đ',	'е'=>'e',	'ж'=>'ž',
			'з'=>'z',	'и'=>'i',	'ј'=>'j',	'к'=>'k',	'л'=>'l',
			'м'=>'m',	'н'=>'n',	'о'=>'o',	'п'=>'p',	'р'=>'r', 
			'с'=>'s',	'ш'=>'š',	'т'=>'t',	'ћ'=>'ć',	'у'=>'u', 
			'ф'=>'f',	'х'=>'h',	'ц'=>'c',	'ч'=>'č'
		));
		
		switch($translation)
		{
			case 'cyr_to_lat' :			
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
				
			case 'lat_to_cyr' :
				$lat_to_cyr = array();
				$lat_to_cyr = array_merge($lat_to_cyr, array_flip($transliteration));
				$lat_to_cyr = array_merge($lat_to_cyr, array(
					'NJ'=>'Њ',	'LJ'=>'Љ',	'DŽ'=>'Џ',	'DJ'=>'Ђ',	'DZ'=>'Ѕ',	'dz'=>'ѕ'
				));
				$lat_to_cyr = apply_filters('rstr/inc/transliteration/sr_RS/lat_to_cyr', $lat_to_cyr);
				
				return str_replace(array_keys($lat_to_cyr), array_values($lat_to_cyr), $content);
				break;
		}
	}

	/*
	 * Russian transliteration
	 * @since     1.0.2
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function ru_RU ($content, $translation = 'cyr_to_lat')
	{
		$transliteration = apply_filters('rstr/inc/transliteration/ru_RU', array(
			// Variations and special characters
			'Ё'=>'Yo',	'Ж'=>'Zh',	'Х'=>'Kh',	'Ц'=>'Ts',	'Ч'=>'Ch',
			'Ш'=>'Sh',	'Щ'=>'Shch','Ю'=>'Ju',	'Я'=>'Ja',	'ё'=>'yo',
			'ж'=>'zh',	'х'=>'kh',	'ц'=>'ts',	'ч'=>'ch',	'ш'=>'sh',
			'щ'=>'shch','ю'=>'ju',	'я'=>'ja',
			
			// All other letters
			'А'=>'A',	'Б'=>'B',	'В'=>'V',	'Г'=>'G',	'Д'=>'D', 
			'Е'=>'E',	'З'=>'Z',	'И'=>'I',	'Й'=>'J',	'К'=>'K',
			'Л'=>'L',	'М'=>'M',	'Н'=>'N',	'О'=>'O',	'П'=>'P',
			'Р'=>'R',	'С'=>'S',	'Т'=>'T',	'У'=>'U',	'Ф'=>'F',
			'Ъ'=>'',	'Ы'=>'Y',	'Ь'=>'',	'Э'=>'E',	'а'=>'a',
			'б'=>'b',	'в'=>'v',	'г'=>'g',	'д'=>'d',	'е'=>'e',	 
			'з'=>'z',	'и'=>'i',	'й'=>'j',	'к'=>'k',	'э'=>'e',
			'л'=>'l',	'м'=>'m',	'н'=>'n',	'о'=>'o',	'п'=>'p', 
			'р'=>'r',	'с'=>'s',	'т'=>'t',	'у'=>'u',	'ф'=>'f', 
			'ъ'=>'',	'ы'=>'y',	'ь'=>''
		));
		
		switch($translation)
		{
			case 'cyr_to_lat' :
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
				
			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = array_merge($transliteration, array(
					'CH'=>'Ч',	'YO'=>'Ё',	'ZH'=>'Ж',	'KH'=>'Х',	'TS'=>'Ц',	'Sh'=>'Ш',	'SCH'=>'Щ',	'YU'=>'Ю',	'YA'=>'Я'
				));
				$transliteration = apply_filters('rstr/inc/transliteration/ru_RU/lat_to_cyr', $transliteration);
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
		}
	}
	
	/*
	 * Belarusian transliteration
	 * @since     1.0.2
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function bel ($content, $translation = 'cyr_to_lat')
	{
		$transliteration = apply_filters('rstr/inc/transliteration/bel', array (
			// Variations and special characters
			'ДЖ'=>'Dž',	'ДЗ'=>'Dz',	'Ё'=>'Io',	'Е'=>'Ie',
			'Х'=>'Ch',	'Ю'=>'Iu',	'Я'=>'Ia',	'дж'=>'dž',
			'дз'=>'dz',	'е'=>'ie',	'ё'=>'io',	'х'=>'ch',
			'ю'=>'iu',	'я'=>'ia',	
			
			// All other letters
			'А'=>'A',	'Б'=>'B',	'В'=>'V',	'Г'=>'H',
			'Д'=>'D',	'Ж'=>'Ž',	'З'=>'Z',	'І'=>'I',
			'Й'=>'J',	'К'=>'K',	'Л'=>'L',	'М'=>'M',
			'Н'=>'N',	'О'=>'O',	'П'=>'P',	'Р'=>'R',
			'СЬ'=>'Ś',	'С'=>'S',	'Т'=>'T',	'У'=>'U',
			'Ў'=>'Ǔ',	'Ф'=>'F',	'Ц'=>'C',	'э'=>'e',
			'Ч'=>'Č',	'Ш'=>'Š',	'Ы'=>'Y',	'Ь'=>'\'',
			'а'=>'a',	'б'=>'b',	'в'=>'v',	'г'=>'h',
			'ж'=>'ž',	'з'=>'z',	'і'=>'i',	'Э'=>'E',
			'й'=>'j',	'к'=>'k',	'л'=>'l',	'м'=>'m',
			'н'=>'n',	'о'=>'o',	'п'=>'p',	'р'=>'r',
			'сь'=>'ś',	'с'=>'s',	'т'=>'t',	'у'=>'u',
			'ў'=>'ǔ',	'ф'=>'f',	'ц'=>'c',	'д'=>'d',
			'ч'=>'č',	'ш'=>'š',	'ы'=>'y',	'ь'=>'\''
		));
		
		switch($translation)
		{
			case 'cyr_to_lat' :
				$sRe = '/(?<=^|\s|\'|’|[IЭЫAУО])';
				$content = preg_replace(
					// For е, ё, ю, я, the digraphs je, jo, ju, ja are used
					// word-initially, and after a vowel, apostrophe (’),
					// separating ь, or ў.
					array (
						$sRe . 'Е/i', $sRe . 'Ё/i', $sRe . 'Ю/i', $sRe . 'Я/i',
						$sRe . 'е/i', $sRe . 'ё/i', $sRe . 'ю/i', $sRe . 'я/i',
					),
					array (
						'Je',	'Jo',	'Ju',	'Ja',	'je',	'jo',	'ju',	'ja',
					),
					$content
				);
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
				
			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = array_merge($transliteration, array(
					'CH'=>'Х',	'DŽ'=>'ДЖ',	'DZ'=>'ДЗ',	'IE'=>'Е',	'IO'=>'Ё',	'IU'=>'Ю',	'IA'=>'Я'
				));
				$transliteration = apply_filters('rstr/inc/transliteration/bel/lat_to_cyr', $transliteration);
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
		}
	}
	
	/*
	 * Bulgarian transliteration
	 * @since     1.0.7
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function bg_BG ($content, $translation = 'cyr_to_lat')
	{
		$transliteration = apply_filters('rstr/inc/transliteration/bg_BG', array (
			// Variations and special characters
			'Ж' => 'Zh',	'ж' => 'zh',	'Ц' => 'Ts',	'ц' => 'ts',	'Ч' => 'Ch',
			'ч' => 'ch',	'Ш' => 'Sh',	'ш' => 'sh',	'Щ' => 'Sht',	'щ' => 'sht',
			'Ю' => 'Yu',	'ю' => 'yu',	'Я' => 'Ya',	'я' => 'ya',
			
			// All other letters
			'А' => 'A',		'а' => 'a',		'Б' => 'B',		'б' => 'b',		'В' => 'V',
			'в' => 'v',		'Г' => 'G',		'г' => 'g',		'Д' => 'D',		'д' => 'd',
			'Е' => 'E',		'е' => 'e',		'З' => 'Z',		'з' => 'z',		'И' => 'I',
			'и' => 'i',		'Й' => 'J',		'й' => 'j',		'К' => 'K',		'к' => 'k',
			'Л' => 'L',		'л' => 'l',		'М' => 'M',		'м' => 'm',		'Н' => 'N',
			'н' => 'n',		'О' => 'O',		'о' => 'o',		'П' => 'P',		'п' => 'p',
			'Р' => 'R',		'р' => 'r',		'С' => 'S',		'с' => 's',		'Т' => 'T',
			'т' => 't',		'У' => 'U',		'у' => 'u',		'Ф' => 'F',		'ф' => 'f',
			'Х' => 'H',		'х' => 'h',		'Ъ' => 'Ǎ',		'ъ' => 'ǎ',		'Ь' => '',
			'ь' => ''
		));
		
		switch($translation)
		{
			case 'cyr_to_lat' :
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
				
			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = array_merge($transliteration, array(
					'ZH'=>'Ж',	'TS'=>'Ц',	'CH'=>'Ч',	'SH'=>'Ш',	'SHT'=>'Щ',	'YU'=>'Ю',	'YA'=>'Я'
				));
				$transliteration = apply_filters('rstr/inc/transliteration/bg_BG/lat_to_cyr', $transliteration);
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
		}
	}
	
	/*
	 * Macedonian transliteration
	 * @since     1.0.7
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function mk_MK ($content, $translation = 'cyr_to_lat')
	{
		$transliteration = apply_filters('rstr/inc/transliteration/mk_MK', array (
			// Variations and special characters
			'Ѓ' => 'Gj',	'ѓ' => 'gj',	'Ѕ' => 'Dz',	'ѕ' => 'dz',	'Њ' => 'Nj',
			'њ' => 'nj',	'Љ' => 'Lj',	'љ' => 'lj',	'Ќ' => 'Kj',	'ќ' => 'kj',
			'Ч' => 'Ch',	'ч' => 'ch',	'Џ' => 'Dj',	'џ' => 'dj',	'Ж' => 'Zh',
			'ж' => 'Zh',	'Ш' => 'Sh',	'ш' => 'sh',
			
			// All other letters
			'А' => 'A',		'а' => 'a',		'Б' => 'B',		'б' => 'b',		'В' => 'V',
			'в' => 'v',		'Г' => 'G',		'г' => 'g',		'Д' => 'D',		'д' => 'd',
			'Е' => 'E',		'е' => 'e',		'З' => 'Z',		'з' => 'z',		'И' => 'I',
			'и' => 'i',		'J' => 'J',		'j' => 'j',		'К' => 'K',		'к' => 'k',
			'Л' => 'L',		'л' => 'l',		'М' => 'M',		'м' => 'm',		'Н' => 'N',
			'н' => 'n',		'О' => 'O',		'о' => 'o',		'П' => 'P',		'п' => 'p',
			'Р' => 'R',		'р' => 'r',		'С' => 'S',		'с' => 's',		'Т' => 'T',
			'т' => 't',		'У' => 'U',		'у' => 'u',		'Ф' => 'F',		'ф' => 'f',
			'Х' => 'H',		'х' => 'h',		'Ъ' => 'Ǎ',		'ъ' => 'ǎ'
		));
		
		switch($translation)
		{
			case 'cyr_to_lat' :
				$sRe = '/(?<=^|\s|\'|’|[IЭЫAУО])';
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
				
			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = array_merge($transliteration, array(
					'ZH'=>'Ж', 'GJ' => 'Ѓ', 'CH'=>'Ч', 'SH'=>'Ш', 'Dz' => 'Ѕ', 'Nj' => 'Њ', 'Lj' => 'Љ', 'KJ' => 'Ќ', 'DJ' => 'Џ' 
				));
				$transliteration = apply_filters('rstr/inc/transliteration/mk_MK/lat_to_cyr', $transliteration);
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
		}
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
		if(!$this->get_locale){
			$this->get_locale = get_locale();
		}
        return $this->get_locale;
	}
	
	/*
	 * Get list of available locales
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public function get_locales( $needle = NULL ){
		$locales = array();
		$locale_file=RSTR_ROOT.'/libraries/locale.lib';
		
		if(file_exists($locale_file))
		{
			if($fopen_locale=fopen($locale_file, 'r'))
			{
				$contents = fread($fopen_locale, filesize($locale_file));
				fclose($fopen_locale);
				
				if(!empty($contents))
				{
					$locales = explode("\n", $contents);
					$locales = array_unique($locales);
					$locales = array_filter($locales);
					$locales = array_map('trim', $locales);
				} else return false;
			} else return false;
		} else return false;
		
		if($needle) {
			return (in_array($needle, $locales, true) !== false ? $needle : false);
		} else {
			return $locales;
		}
	}
	
	/*
	 * Exclude words or sentences for Cyrillic
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_exclude_list(){
		return apply_filters('rstr/init/exclude/cyr', array());
	}
	
	/*
	 * Exclude words or sentences for Latin
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function lat_exclude_list(){
		return apply_filters('rstr/init/exclude/lat', array());
	}
}
endif;