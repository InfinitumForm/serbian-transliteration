<?php if ( !defined('WPINC') ) die();
/**
 * Arabic transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_ar {

	public static $map = array (
		// Variations and special characters
		"ا"=> "a",	"أ"=> "a",	"إ"=> "ie",	"آ"=> "aa",
		"ب"=> "b",	"ت"=> "t",	"ث"=> "th",	"ج"=> "j",
		"ح"=> "h",	"خ"=> "kh",	"د"=> "d",	"ذ"=> "thz",
		"ر"=> "r",	"ز"=> "z",	"س"=> "s",	"ش"=> "sh",
		"ص"=> "ss",	"ض"=> "dt",	"ط"=> "td",	"ظ"=> "thz",
		"ع"=> "a",	"غ"=> "gh",	"ف"=> "f",	"ق"=> "q",
		"ك"=> "k",	"ل"=> "l",	"م"=> "m",	"ن"=> "n",
		"ه"=> "h",	"و"=> "w",	"ي"=> "e",	"اي"=> "i",
		"ة"=> "tt",	"ئ"=> "ae",	"ى"=> "a",	"ء"=> "aa",
		"ؤ"=> "uo",	"َ"=> "a",	"ُ"=> "u",	"ِ"=> "e",
		" ٌ"=> "on",	"ٍ"=> "en",	"ً"=> "an",	"تش"=> "tsch",
		// Numbers - specific for arabic
		'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3',
		'٤' => '4', '۵' => '5', '٦' => '6', '۷' => '7', 
		'۸' => '8', '۹' => '9', '.' => '.'
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_ar', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/ar', [$transliteration], '2.0.0', 'transliteration_map_ar');

		switch($translation)
		{
			case 'cyr_to_lat' :
				$content = strtr($content, $transliteration);
				
				 // Additional rules for customization
				// Normalization of Hamza characters
				$content = str_replace(array("أ", "إ", "آ", "ؤ", "ئ"), "a", $content);

				// Adaptation of Ta' Marbut
				$content = preg_replace('/ة\b/', 'h', $content); // at the end of the word
				$content = str_replace('ة', 't', $content);      // in all other cases

				// Simplifying long vowels
				$content = str_replace(array("وو", "يي"), array('w', 'y'), $content);

				// Contextual adaptation of diacritical marks
				$content = str_replace(array("َ", "ُ", "ِ", "ً", "ٌ", "ٍ"), '', $content);

				// Simplifying the initial Alif lam (ال)
				$content = preg_replace('/\bال/', 'al', $content);

				// Special characters
				$content = str_replace(array("ﻟﺎ", "ﻻ"), 'la', $content); // Ligatura Lam-Alif

				return $content;
				
				break;

			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				
				// Processing of special cases or combinations
				$content = preg_replace('/aa/', 'آ', $content); // Long vocal "aa"
				$content = preg_replace('/uu/', 'وو', $content); // Long vocal "uu"
				$content = preg_replace('/ii/', 'يي', $content); // Long vocal "ii"
				
				$transliteration = array_flip($transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/ar/lat_to_cyr', $transliteration);
				$content = strtr($content, $transliteration);
				
				return $content;
				break;
		}

		return $content;
	}
}