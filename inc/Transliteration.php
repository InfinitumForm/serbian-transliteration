<?php
/**
 * Transliterating Mode by locale
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Transliterating')) :
class Serbian_Transliteration_Transliterating {
	
	/*
	 * Serbian transliteration
	 * @since     1.0.2
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function sr_RS ($content, $translation = 'cyr_to_lat')
	{
		$transliteration = apply_filters('serbian_transliteration_transliterating_sr_RS', array(
			'А'=>'A',	'Б'=>'B',	'В'=>'V',	'Г'=>'G',	'Д'=>'D', 
			'Ђ'=>'Đ',	'Е'=>'E',	'Ж'=>'Ž',	'З'=>'Z',	'И'=>'I', 
			'Ј'=>'J',	'К'=>'K',	'Л'=>'L',	'Љ'=>'Lj',	'М'=>'M', 
			'Н'=>'N',	'Њ'=>'Nj',	'О'=>'O',	'П'=>'P',	'Р'=>'R', 
			'С'=>'S',	'Ш'=>'Š',	'Т'=>'T',	'Ћ'=>'Ć',	'У'=>'U', 
			'Ф'=>'F',	'Х'=>'H',	'Ц'=>'C',	'Ч'=>'Č',	'Џ'=>'Dž', 
			'а'=>'a',	'б'=>'b',	'в'=>'v',	'г'=>'g',	'д'=>'d', 
			'ђ'=>'đ',	'е'=>'e',	'ж'=>'ž',	'з'=>'z',	'и'=>'i', 
			'ј'=>'j',	'к'=>'k',	'л'=>'l',	'љ'=>'lj',	'м'=>'m', 
			'н'=>'n',	'њ'=>'nj',	'о'=>'o',	'п'=>'p',	'р'=>'r', 
			'с'=>'s',	'ш'=>'š',	'т'=>'t',	'ћ'=>'ć',	'у'=>'u', 
			'ф'=>'f',	'х'=>'h',	'ц'=>'c',	'ч'=>'č',	'џ'=>'dž'
		));
		
		switch($translation)
		{
			case 'cyr_to_lat' :
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
				
			case 'lat_to_cyr' :
				$transliteration = array_merge(array(
					'NJ'=>'Њ',	'LJ'=>'Љ',	'DŽ'=>'Џ',	'DJ'=>'Ђ',	'dz'=>'ѕ',	'DZ'=>'Ѕ',	'Ć'=>'Ћ'
				), $transliteration);
				$transliteration = array_flip($transliteration);
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
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
		$transliteration = apply_filters('serbian_transliteration_transliterating_ru_RU', array(
			'А'=>'A',	'Б'=>'B',	'В'=>'V',	'Г'=>'G',	'Д'=>'D', 
			'Е'=>'E',	'Ё'=>'Yo',	'Ж'=>'Zh',	'З'=>'Z',	'И'=>'I', 
			'Й'=>'J',	'К'=>'K',	'Л'=>'L',	'М'=>'M',	'Н'=>'N', 
			'О'=>'O',	'П'=>'P',	'Р'=>'R',	'С'=>'S',	'Т'=>'T', 
			'У'=>'U',	'Ф'=>'F',	'Х'=>'Kh',	'Ц'=>'Ts',	'Ч'=>'Ch', 
			'Ш'=>'Sh',	'Щ'=>'Sch',	'Ъ'=>'',	'Ы'=>'Y',	'Ь'=>'', 
			'Э'=>'E',	'Ю'=>'Yu',	'Я'=>'Ya',	'а'=>'a',	'б'=>'b', 
			'в'=>'v',	'г'=>'g',	'д'=>'d',	'е'=>'e',	'ё'=>'yo', 
			'ж'=>'zh',	'з'=>'z',	'и'=>'i',	'й'=>'j',	'к'=>'k', 
			'л'=>'l',	'м'=>'m',	'н'=>'n',	'о'=>'o',	'п'=>'p', 
			'р'=>'r',	'с'=>'s',	'т'=>'t',	'у'=>'u',	'ф'=>'f', 
			'х'=>'kh',	'ц'=>'ts',	'ч'=>'ch',	'ш'=>'sh',	'щ'=>'sch', 
			'ъ'=>'',	'ы'=>'y',	'ь'=>'',	'э'=>'e',	'ю'=>'yu', 
			'я'=>'ya'
		));
		
		switch($translation)
		{
			case 'cyr_to_lat' :
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
				
			case 'lat_to_cyr' :
				$transliteration = array_merge(array(
					'CH'=>'Ч',	'YO'=>'Ё',	'ZH'=>'Ж',	'KH'=>'Х',	'TS'=>'Ц',	'Sh'=>'Ш',	'SCH'=>'Щ',	'YU'=>'Ю',	'YA'=>'Я'
				), $transliteration);
				$transliteration = array_flip($transliteration);
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
		$transliteration = apply_filters('serbian_transliteration_transliterating_bel', array (
			// upper case
			'А'=>'A',	'Б'=>'B',	'В'=>'V',	'Г'=>'H',
			'Д'=>'D',	'ДЖ'=>'Dž',	'ДЗ'=>'Dz',	'Е'=>'Ie',
			'Ё'=>'Io',	'Ж'=>'Ž',	'З'=>'Z',	'І'=>'I',
			'Й'=>'J',	'К'=>'K',	'Л'=>'L',	'М'=>'M',
			'Н'=>'N',	'О'=>'O',	'П'=>'P',	'Р'=>'R',
			'СЬ'=>'Ś',	'С'=>'S',	'Т'=>'T',	'У'=>'U',
			'Ў'=>'Ǔ',	'Ф'=>'F',	'Х'=>'Ch',	'Ц'=>'C',
			'Ч'=>'Č',	'Ш'=>'Š',	'Ы'=>'Y',	'Ь'=>'\'',
			'Э'=>'E',	'Ю'=>'Iu',	'Я'=>'Ia',	'’'=>'',
			// lower case
			'а'=>'a',	'б'=>'b',	'в'=>'v',	'г'=>'h',
			'д'=>'d',	'дж'=>'dž',	'дз'=>'dz',	'е'=>'ie',
			'ё'=>'io',	'ж'=>'ž',	'з'=>'z',	'і'=>'i',
			'й'=>'j',	'к'=>'k',	'л'=>'l',	'м'=>'m',
			'н'=>'n',	'о'=>'o',	'п'=>'p',	'р'=>'r',
			'сь'=>'ś',	'с'=>'s',	'т'=>'t',	'у'=>'u',
			'ў'=>'ǔ',	'ф'=>'f',	'х'=>'ch',	'ц'=>'c',
			'ч'=>'č',	'ш'=>'š',	'ы'=>'y',	'ь'=>'\'',
			'э'=>'e',	'ю'=>'iu',	'я'=>'ia',	'\''=>'',
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
				$transliteration = array_merge(array(
					'CH'=>'Х',	'DŽ'=>'ДЖ',	'DZ'=>'ДЗ',	'IE'=>'Е',	'IO'=>'Ё',	'IU'=>'Ю',	'IA'=>'Я'
				), $transliteration);
				$transliteration = array_flip($transliteration);
				return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				break;
		}
	}
}
endif;