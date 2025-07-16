<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Ukrainian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_uk
{
    public static $map = [
        // Special digraphs
        'ЗГ' => 'ZGH', 'Зг' => 'Zgh', 'зг' => 'zgh',

        // Complex letters
        'Є' => 'Ye', 'є' => 'ye',
        'Ї' => 'Yi', 'ї' => 'yi',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',

        // Basic letters
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'H', 'г' => 'h',
        'Ґ' => 'G', 'ґ' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z',
        'И' => 'Y', 'и' => 'y',
        'І' => 'I', 'і' => 'i',
        'Й' => 'Y', 'й' => 'y',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'Kh', 'х' => 'kh',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',

        // Soft sign & obsolete characters
        'Ь' => '', 'ь' => '',
        'Ъ' => '', 'ъ' => '',

        // Misc
        '№' => 'No',
        "'" => '',
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

        $map = apply_filters('transliteration_map_uk', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/uk', [$map], '2.0.0', 'transliteration_map_uk');

        switch ($translation) {
            case 'cyr_to_lat':
                // Special beginning-of-word replacements
                $content = preg_replace(
                    ['/\bЄ/u', '/\bє/u', '/\bЇ/u', '/\bї/u', '/\bЮ/u', '/\bю/u', '/\bЯ/u', '/\bя/u'],
                    ['Ye',     'ye',     'Yi',     'yi',     'Yu',     'yu',     'Ya',     'ya'],
                    $content
                );

                return apply_filters('rstr/inc/transliteration/uk/cyr_to_lat', strtr($content, $map));

            case 'lat_to_cyr':
                $reverse = array_filter($map, static fn($v) => $v !== '');
                $reverse = array_flip($reverse);

                // Override with digraphs and complex letters
                $custom = [
                    'ZGH' => 'ЗГ', 'Zgh' => 'Зг', 'zgh' => 'зг',
                    'SHCH' => 'Щ', 'Shch' => 'Щ', 'shch' => 'щ',
                    'YE' => 'Є', 'Ye' => 'Є', 'ye' => 'є',
                    'YI' => 'Ї', 'Yi' => 'Ї', 'yi' => 'ї',
                    'YU' => 'Ю', 'Yu' => 'Ю', 'yu' => 'ю',
                    'YA' => 'Я', 'Ya' => 'Я', 'ya' => 'я',
                    'KH' => 'Х', 'Kh' => 'Х', 'kh' => 'х',
                    'TS' => 'Ц', 'Ts' => 'Ц', 'ts' => 'ц',
                    'CH' => 'Ч', 'Ch' => 'Ч', 'ch' => 'ч',
                    'SH' => 'Ш', 'Sh' => 'Ш', 'sh' => 'ш',
                    'ZH' => 'Ж', 'Zh' => 'Ж', 'zh' => 'ж',
                ];

                $reverse = array_merge($custom, $reverse);

                // Sort by descending length of key for longest match first
                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                // Word-initial Latin sequences
                $initial_lat = [
                    '/\bYe/u' => 'Є', '/\bye/u' => 'є',
                    '/\bYi/u' => 'Ї', '/\byi/u' => 'ї',
                    '/\bYu/u' => 'Ю', '/\byu/u' => 'ю',
                    '/\bYa/u' => 'Я', '/\bya/u' => 'я',
                    '/\bY/u'  => 'Й', '/\by/u'  => 'й',
                ];
                $content = preg_replace(array_keys($initial_lat), array_values($initial_lat), $content);

                $output = str_replace(array_keys($reverse), array_values($reverse), $content);

                return apply_filters('rstr/inc/transliteration/uk/lat_to_cyr', $output);

            default:
                return $content;
        }
    }
}
