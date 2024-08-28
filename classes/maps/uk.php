<?php if ( !defined('WPINC') ) die();
/**
 * Ukrainian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_uk {

	public static $map = array (
		// Special variations
		'ЗГ' => 'ZGH',	'Зг' => 'Zgh',	'зг' => 'zgh',
		
		' Є' => ' Ye',	' є' => ' ye',
		' Ї' => ' Yi',	' ї' => ' yi',	' Й' => ' Y',	' й' => ' y',
		' Ю' => ' Yu',	' ю' => ' yu',	' Я' => ' Ya',	' я' => ' ya',
		
		// Variations and special characters
		'Є' => 'Ie',	'є' => 'ie',	'Ї' => 'i',	'ї' => 'i',	'Щ' => 'Shch',
		'щ' => 'shch',	'Ю' => 'Iu',	'ю' => 'iu',	'Я' => 'Ia',	'я' => 'ia',

		// All other letters
		'А' => 'A',		'а' => 'a',		'Б' => 'B',		'б' => 'b',		'В' => 'V',
		'в' => 'v',		'Г' => 'H',		'г' => 'h',		'Д' => 'D',		'д' => 'd',
		'Е' => 'E',		'е' => 'e',		'Ж' => 'Zh',	'ж' => 'zh',	'З' => 'Z',
		'з' => 'z',		'И' => 'Y',		'и' => 'y',		'І' => 'I',		'і' => 'i',
		'Й' => 'J',		'й' => 'j',		'К' => 'K',		'к' => 'k',		'Л' => 'L',
		'л' => 'l',		'М' => 'M',		'м' => 'm',		'Н' => 'N',		'н' => 'n',
		'О' => 'O',		'о' => 'o',		'П' => 'P',		'п' => 'p',		'Р' => 'R',
		'р' => 'r',		'С' => 'S',		'с' => 's',		'Т' => 'T',		'т' => 't',
		'У' => 'U',		'у' => 'u',		'Ф' => 'F',		'ф' => 'f',		'Х' => 'Kh',
		'х' => 'kh',	'Ц' => 'Ts',	'ц' => 'ts',	'Ч' => 'Ch',	'ч' => 'ch',
		'Ш' => 'Sh',	'ш' => 'sh',	'Ґ' => 'G',		'ґ' => 'g',		'Ь' => '',
		'ь' => '',		'\'' => ''
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_uk', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/uk', [$transliteration], '2.0.0', 'transliteration_map_uk');

		switch($translation)
		{
			case 'cyr_to_lat' :
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_merge(array(
					'SHCH' => 'Щ',	'IE' => 'Є',	'YE' => 'Є',	'YU' => 'Ю',	'IU' => 'Ю',	'YA' => 'Я',	'IA' => 'Я',	'YI' => 'Ї',
					'KH' => 'Х',	'TS' => 'Ц',	'CH' => 'Ч',	'SH' => 'Ш',	'ZH' => 'Ж',
				), $transliteration);
				$transliteration = array_flip($transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/uk/lat_to_cyr', $transliteration);
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}