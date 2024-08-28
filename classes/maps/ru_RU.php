<?php if ( !defined('WPINC') ) die();
/**
 * Russian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_ru_RU {

	public static $map = array(
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
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_ru_RU', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/ru_RU', [$transliteration], '2.0.0', 'transliteration_map_ru_RU');

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
				$transliteration = array_flip($transliteration);
				$transliteration = array_merge(array(
					'CH'=>'Ч',	'YO'=>'Ё',	'ZH'=>'Ж',	'KH'=>'Х',	'TS'=>'Ц',	'Sh'=>'Ш',	'SCH'=>'Щ',	'YU'=>'Ю',	'YA'=>'Я'
				), $transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/ru_RU/lat_to_cyr', $transliteration);
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}