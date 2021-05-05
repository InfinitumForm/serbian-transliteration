<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Arabic transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */
if(!class_exists('Serbian_Transliteration_ar')) :
class Serbian_Transliteration_ar {

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

		$transliteration = apply_filters('rstr/inc/transliteration/ar', self::$map);

		switch($translation)
		{
			case 'cyr_to_lat' :
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/ar/lat_to_cyr', $transliteration);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}
endif;
