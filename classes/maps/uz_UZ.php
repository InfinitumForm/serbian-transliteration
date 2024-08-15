<?php if ( !defined('WPINC') ) die();
/**
 * Uzbek transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.12.1
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_uz_UZ {

	public static $map = array(
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ф' => 'F', 'ф' => 'f',
        'Г' => 'G', 'г' => 'g',
        'Ҳ' => 'H', 'ҳ' => 'h',
        'И' => 'I', 'и' => 'i',
        'Й' => 'Y', 'й' => 'y',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p',
        'Қ' => 'Q', 'қ' => 'q',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ў' => 'O‘', 'ў' => 'o‘',
        'В' => 'V', 'в' => 'v',
        'Х' => 'X', 'х' => 'x',
        'Ъ' => '',
        'Ь' => '',
        'Ц' => 'S', 'ц' => 's',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Э' => 'E', 'э' => 'e',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',
        'Ғ' => 'G‘', 'ғ' => 'g‘',
        'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z'
    );

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_uz_UZ', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/uz_UZ', [$transliteration], '2.0.0', 'transliteration_map_uz_UZ');

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
				$transliteration = array_merge(array(
					'SHCH' => 'Щ', 'shch' => 'щ',
					'GH' => 'Ғ', 'gh' => 'ғ',
					'YO' => 'Ё', 'yo' => 'ё',
					'ZH' => 'Ж', 'zh' => 'ж',
					'NG' => 'Ң', 'ng' => 'ң',
					'KH' => 'Х', 'kh' => 'х',
					'SH' => 'Ш', 'sh' => 'ш',
					'YA' => 'Я', 'ya' => 'я',
					'YU' => 'Ю', 'yu' => 'ю',
					'CH' => 'Ч', 'ch' => 'ч',
					'TS' => 'Ц', 'ts' => 'ц',
					'J' => 'Й', 'j' => 'й',
					'I' => 'И', 'i' => 'и'
				), $transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/uz_UZ/lat_to_cyr', $transliteration);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}