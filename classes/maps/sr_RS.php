<?php if ( !defined('WPINC') ) die();
/**
 * Serbian language
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */
class Transliteration_Map_sr_RS {

	public static $map = array(
		// Variations and special characters
		'ња' => 'nja', 	'ње' => 'nje', 	'њи' => 'nji',	'њо' => 'njo',
		'њу' => 'nju',	'ља' => 'lja',	'ље' => 'lje',	'љи' => 'lji',	'љо' => 'ljo',
		'љу' => 'lju',	'џа' => 'dža',	'џе' => 'dže',	'џи' => 'dži',	'џо' => 'džo',
		'џу' => 'džu',

		'Ња' => 'Nja', 	'Ње' => 'Nje', 	'Њи' => 'Nji',	'Њо' => 'Njo',
		'Њу' => 'Nju',	'Ља' => 'Lja',	'Ље' => 'Lje',	'Љи' => 'Lji',	'Љо' => 'Ljo',
		'Љу' => 'Lju',	'Џа' => 'Dža',	'Џе' => 'Dže',	'Џи' => 'Dži',	'Џо' => 'Džo',
		'Џу' => 'Džu',

		'џ'=>'dž',		'Џ'=>'DŽ',		'љ'=>'lj',		'Љ'=>'LJ', 		'њ'=>'nj',
		'Њ'=>'NJ',

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
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)){
			return $content;
		}

		$transliteration = apply_filters('transliteration_map_sr_RS', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/sr_RS', [$transliteration], '2.0.0', 'transliteration_map_sr_RS');

		switch($translation)
		{
			case 'cyr_to_lat' :
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$lat_to_cyr = array();
				$lat_to_cyr = array_merge($lat_to_cyr, array_flip($transliteration));
				$lat_to_cyr = array_merge(array(
					'NJ'=>'Њ',	'LJ'=>'Љ',	'DŽ'=>'Џ',	'DJ'=>'Ђ',	'DZ'=>'Ѕ',	'dz'=>'ѕ'
				), $lat_to_cyr);
				$lat_to_cyr = apply_filters('rstr/inc/transliteration/sr_RS/lat_to_cyr', $lat_to_cyr);

			//	return str_replace(array_keys($lat_to_cyr), array_values($lat_to_cyr), $content);
				$content = strtr($content, $lat_to_cyr);
				
				// Fix some special words
				$content = str_replace(array(
					'оџљебња',
					'ОЏЉЕБЊА'
				), array(
					'оджљебња',
					'ОДЖЉЕБЊА'
				), $content);
				
				return $content;
				break;
		}

		return $content;
	}
}