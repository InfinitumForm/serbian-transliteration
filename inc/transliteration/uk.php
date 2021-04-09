<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Ukrainian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */
if(!class_exists('Serbian_Transliteration_uk')) :
class Serbian_Transliteration_uk {
	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;
		
		$transliteration = apply_filters('rstr/inc/transliteration/uk', array (
			// Variations and special characters
			'Є' => 'Je',	'є' => 'je',	'Ї' => 'Ji',	'ї' => 'ji',	'Щ' => 'Šč',
			'щ' => 'šč',	'Ю' => 'Ju',	'ю' => 'ju',	'Я' => 'Ja',	'я' => 'ja',

			// All other letters
			'А' => 'A',		'а' => 'a',		'Б' => 'B',		'б' => 'b',		'В' => 'V',
			'в' => 'v',		'Г' => 'H',		'г' => 'h',		'Д' => 'D',		'д' => 'd',
			'Е' => 'E',		'е' => 'e',		'Ж' => 'Ž',		'ж' => 'ž',		'З' => 'Z',
			'з' => 'z',		'И' => 'Y',		'и' => 'y',		'I' => 'I',		'i' => 'i',
			'Й' => 'J',		'й' => 'j',		'К' => 'K',		'к' => 'k',		'Л' => 'L',
			'л' => 'l',		'М' => 'M',		'м' => 'm',		'Н' => 'N',		'н' => 'n',
			'О' => 'O',		'о' => 'o',		'П' => 'P',		'п' => 'p',		'Р' => 'R',
			'р' => 'r',		'С' => 'S',		'с' => 's',		'Т' => 'T',		'т' => 't',
			'У' => 'U',		'у' => 'u',		'Ф' => 'F',		'ф' => 'f',		'Х' => 'h',
			'х' => 'h',		'Ц' => 'C',		'ц' => 'c',		'Ч' => 'Č',		'ч' => 'č',
			'Ш' => 'Š',		'ш' => 'š',		'Ґ' => 'G',		'ґ' => 'g',		'Ь' => '\'',
			'ь' => '\''
		));
		
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
					'ŠČ' => 'Щ',	'JE' => 'Є',	'JU' => 'Ю',	'JA' => 'Я',	'JI' => 'Ї',
					'KH' => 'Х',	'Kh' => 'Х',	'kh' => 'х'
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
endif;