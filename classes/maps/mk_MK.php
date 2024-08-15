<?php if ( !defined('WPINC') ) die();
/**
 * Macedonian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_mk_MK {

	public static $map = array (
		// Variations and special characters
		'Ѓ' => 'Gj',	'ѓ' => 'gj',	'Ѕ' => 'Dz',	'ѕ' => 'dz',	'Њ' => 'Nj',
		'њ' => 'nj',	'Љ' => 'Lj',	'љ' => 'lj',	'Ќ' => 'Kj',	'ќ' => 'kj',
		'Ч' => 'Ch',	'ч' => 'ch',	'Џ' => 'Dj',	'џ' => 'dj',	'Ж' => 'zh',
		'ж' => 'zh',	'Ш' => 'Sh',	'ш' => 'sh',

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
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_mk_MK', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/mk_MK', [$transliteration], '2.0.0', 'transliteration_map_mk_MK');

		switch($translation)
		{
			case 'cyr_to_lat' :
				$sRe = '/(?<=^|\s|\'|’|[IЭЫAУО])';
//				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = array_merge(array(
					'ZH'=>'Ж', 'GJ' => 'Ѓ', 'CH'=>'Ч', 'SH'=>'Ш', 'Dz' => 'Ѕ', 'Nj' => 'Њ', 'Lj' => 'Љ', 'KJ' => 'Ќ', 'DJ' => 'Џ'
				), $transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/mk_MK/lat_to_cyr', $transliteration);
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}