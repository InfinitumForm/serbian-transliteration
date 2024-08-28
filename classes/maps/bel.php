<?php if ( !defined('WPINC') ) die();
/**
 * Belarusian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_bel {

	public static $map = array (
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
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_bel', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/bel', [$transliteration], '2.0.0', 'transliteration_map_bel');

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
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = array_merge(array(
					'CH'=>'Х',	'DŽ'=>'ДЖ',	'DZ'=>'ДЗ',	'IE'=>'Е',	'IO'=>'Ё',	'IU'=>'Ю',	'IA'=>'Я'
				), $transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/bel/lat_to_cyr', $transliteration);
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}