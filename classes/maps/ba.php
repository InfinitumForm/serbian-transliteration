<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Bashkir (ba) transliteration map
 *
 * @link              http://infinitumform.com/
 * @since             1.12.1
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_ba
{
    public static $map = [
        'А' => 'A', 'а' => 'a',
        'Ә' => 'Ä', 'ә' => 'ä',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Ғ' => 'Ğ', 'ғ' => 'ğ',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ё' => 'Yo', 'ё' => 'yo',
        'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Й' => 'Y', 'й' => 'y',
        'К' => 'K', 'к' => 'k',
        'Ҡ' => 'Q', 'ҡ' => 'q',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'Ң' => 'Ñ', 'ң' => 'ñ',
        'О' => 'O', 'о' => 'o',
        'Ө' => 'Ö', 'ө' => 'ö',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Ҫ' => 'Ś', 'ҫ' => 'ś',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ү' => 'Ü', 'ү' => 'ü',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'H', 'х' => 'h',
        'Һ' => 'H', 'һ' => 'h',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ъ' => 'ʼ', 'ъ' => 'ʼ',
        'Ы' => 'Y', 'ы' => 'y',
        'Ь' => 'ʼ', 'ь' => 'ʼ',
        'Э' => 'E', 'э' => 'e',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',
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
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_ba', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/ba', [$map], '2.0.0', 'transliteration_map_ba');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn ($v) => $v !== ''));
                $custom = [
                    'Shch' => 'Щ', 'shch' => 'щ',
                    'Zh' => 'Ж', 'zh' => 'ж',
                    'Yo' => 'Ё', 'yo' => 'ё',
                    'Yu' => 'Ю', 'yu' => 'ю',
                    'Ya' => 'Я', 'ya' => 'я',
                    'Ch' => 'Ч', 'ch' => 'ч',
                    'Sh' => 'Ш', 'sh' => 'ш',
                    'Ts' => 'Ц', 'ts' => 'ц',
                    'Ğ' => 'Ғ', 'ğ' => 'ғ',
                    'Ñ' => 'Ң', 'ñ' => 'ң',
                    'Ś' => 'Ҫ', 'ś' => 'ҫ',
                    'Ö' => 'Ө', 'ö' => 'ө',
                    'Ü' => 'Ү', 'ü' => 'ү',
                    'Ä' => 'Ә', 'ä' => 'ә',
                ];

                $reverse = array_merge($custom, $reverse);
                uksort($reverse, fn ($a, $b) => strlen($b) <=> strlen($a));
                return str_replace(array_keys($reverse), array_values($reverse), $content);

            default:
                return $content;
        }
    }
}
