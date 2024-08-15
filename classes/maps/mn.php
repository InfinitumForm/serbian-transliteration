<?php if ( !defined('WPINC') ) die();
/**
 * Mongolian
 *
 * @link              http://infinitumform.com/
 * @since             1.12.1
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_mn {

	public static $map = array(
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ё' => 'Yo', 'ё' => 'yo',
        'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Й' => 'Y', 'й' => 'y',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'Ө' => 'Ö', 'ө' => 'ö',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ү' => 'Ü', 'ү' => 'ü',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'Kh', 'х' => 'kh',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ъ' => 'ʼ', 'ъ' => 'ʼ', // tvrdi znak
        'Ы' => 'Y', 'ы' => 'y',
        'Ь' => 'ʼ', 'ь' => 'ʼ', // meki znak
        'Э' => 'E', 'э' => 'e',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya'
    );

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_mn', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/mn', [$transliteration], '2.0.0', 'transliteration_map_mn');

		switch($translation)
		{
			case 'cyr_to_lat' :
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_flip($transliteration);
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = apply_filters('rstr/inc/transliteration/mn/lat_to_cyr', $transliteration);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}