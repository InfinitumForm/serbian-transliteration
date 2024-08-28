<?php if ( !defined('WPINC') ) die();
/**
 * Kazakh transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_kk {

	public static $map = array (
		// Variations and special characters
		'Ғ' => 'Gh',	'ғ' => 'gh',		'Ё' => 'Yo',		'ё' => 'yo',		'Ж' => 'Zh',
		'ж' => 'zh',	'Ң' => 'Ng',		'ң' => 'ng',		'Х' => 'Kh',		'х' => 'kh',
		'Ц' => 'Ts',	'ц' => 'ts',		'Ч' => 'Ch',		'ч' => 'ch',		'Ш' => 'Sh',
		'ш' => 'sh',	'Щ' => 'Shch',		'щ' => 'shch',		'Ю' => 'Yu',		'ю' => 'yu',
		'Я' => 'Ya',	'я' => 'ya',

		// All other letters
		'А' => 'A',		'а' => 'a',		'Б' => 'B',		'б' => 'b',		'В' => 'V',
		'в' => 'v',		'Г' => 'G',		'г' => 'g',		'Д' => 'D',		'д' => 'd',
		'Е' => 'E',		'е' => 'e',		'З' => 'Z',		'з' => 'z',		'И' => 'Ī',
		'и' => 'ī',		'Й' => 'Y',		'й' => 'y',		'К' => 'K',		'к' => 'k',
		'Л' => 'L',		'л' => 'l',		'М' => 'M',		'м' => 'm',		'Н' => 'N',
		'н' => 'n',		'О' => 'O',		'о' => 'o',		'П' => 'P',		'п' => 'p',
		'Р' => 'R',		'р' => 'r',		'С' => 'S',		'с' => 's',		'Т' => 'T',
		'т' => 't',		'У' => 'Ū',		'у' => 'ū',		'Ф' => 'F',		'ф' => 'f',
		'Ү' => 'Ü',		'ү' => 'ü',		'Һ' => 'H',		'һ' => 'h',		'Э' => 'Ė',
		'э' => 'ė',		'Ұ' => 'U',		'ұ' => 'u',		'Ө' => 'Ö',		'ө' => 'ö',
		'Қ' => 'Q',		'қ' => 'q',		'ь' => '',		'І' => 'I',		'і' => 'i',
		'Ъ' => '',		'ъ' => '',		'Ь' => '',		'ь' => ''
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_kk', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/kk', [$transliteration], '2.0.0', 'transliteration_map_kk');

		switch($translation)
		{
			case 'cyr_to_lat' :
//				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_merge(array(
					'SHCH'=>'Щ', 'GH' => 'Ғ', 'YO' => 'Ё', 'ZH'=>'Ж', 'NG'=>'Ң', 'KH'=>'Х', 'SH'=>'Ш', 'YA'=>'Я', 'YU'=>'Ю',
					'CH'=>'Ч', 'TS'=>'Ц', 'SHCH'=>'Щ', 'J'=>'Й', 'j' => 'й', 'I'=>'И', 'i' => 'и'
				), $transliteration);
				$transliteration = array_flip($transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/kk/lat_to_cyr', $transliteration);
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}