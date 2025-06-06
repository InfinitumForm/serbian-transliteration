<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Bulgarian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_bg_BG
{
    public static $map = [
        // Variations and special characters
        'Ж' => 'Zh',	'ж' => 'zh',	'Ц' => 'Ts',	'ц' => 'ts',	'Ч' => 'Ch',
        'ч' => 'ch',	'Ш' => 'Sh',	'ш' => 'sh',	'Щ' => 'Sht',	'щ' => 'sht',
        'Ю' => 'Yu',	'ю' => 'yu',	'Я' => 'Ya',	'я' => 'ya',

        // All other letters
        'А' => 'A',		'а' => 'a',		'Б' => 'B',		'б' => 'b',		'В' => 'V',
        'в' => 'v',		'Г' => 'G',		'г' => 'g',		'Д' => 'D',		'д' => 'd',
        'Е' => 'E',		'е' => 'e',		'З' => 'Z',		'з' => 'z',		'И' => 'I',
        'и' => 'i',		'Й' => 'J',		'й' => 'j',		'К' => 'K',		'к' => 'k',
        'Л' => 'L',		'л' => 'l',		'М' => 'M',		'м' => 'm',		'Н' => 'N',
        'н' => 'n',		'О' => 'O',		'о' => 'o',		'П' => 'P',		'п' => 'p',
        'Р' => 'R',		'р' => 'r',		'С' => 'S',		'с' => 's',		'Т' => 'T',
        'т' => 't',		'У' => 'U',		'у' => 'u',		'Ф' => 'F',		'ф' => 'f',
        'Х' => 'H',		'х' => 'h',		'Ъ' => 'Ǎ',		'ъ' => 'ǎ',		'Ь' => '',
        'ь' => '',
    ];

    /**
     * Transliterate text between Cyrillic and Latin.
     *
     * @param mixed $content String to transliterate.
     * @param string $translation Conversion direction.
     * @return mixed
     */
    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) {
            return $content;
        }

        $transliteration = apply_filters('transliteration_map_bg_BG', self::$map);
        $transliteration = apply_filters_deprecated('rstr/inc/transliteration/bg_BG', [$transliteration], '2.0.0', 'transliteration_map_bg_BG');

        switch ($translation) {
            case 'cyr_to_lat':
                //	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
                return strtr($content, $transliteration);

            case 'lat_to_cyr':
                $transliteration = array_filter($transliteration, fn ($t): bool => $t != '');
                $transliteration = array_flip($transliteration);
                $transliteration = array_merge([
                    'ZH' => 'Ж',	'TS' => 'Ц',	'CH' => 'Ч',	'SH' => 'Ш',	'SHT' => 'Щ',	'YU' => 'Ю',	'YA' => 'Я',
                ], $transliteration);
                $transliteration = apply_filters('rstr/inc/transliteration/bg_BG/lat_to_cyr', $transliteration);
                //	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
                return strtr($content, $transliteration);
        }

        return $content;
    }
}
