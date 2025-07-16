<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Tajik (Tajikistan) transliteration map
 *
 * @link              http://infinitumform.com/
 * @since             1.12.1
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_tg
{
    public static $map = [
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
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'Kh', 'х' => 'kh',
        'Ҷ' => 'J', 'ҷ' => 'j',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Ъ' => 'ʼ', 'ъ' => 'ʼ',
        'Э' => 'E', 'э' => 'e',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',
        'Ҳ' => 'H', 'ҳ' => 'h',
        'Ғ' => "G'", 'ғ' => "g'",   // apostrophized G
        'Ӯ' => 'U', 'ӯ' => 'u',
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

        $map = apply_filters('transliteration_map_tg', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/tg', [$map], '2.0.0', 'transliteration_map_tg');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));

                // Digraph priority for Tajik
                $custom = [
                    'Yo' => 'Ё', 'yo' => 'ё',
                    'Yu' => 'Ю', 'yu' => 'ю',
                    'Ya' => 'Я', 'ya' => 'я',
                    'Zh' => 'Ж', 'zh' => 'ж',
                    'Ch' => 'Ч', 'ch' => 'ч',
                    'Sh' => 'Ш', 'sh' => 'ш',
                    "G'" => 'Ғ', "g'" => 'ғ',
                    'Kh' => 'Х', 'kh' => 'х',
                ];

                $reverse = array_merge($custom, $reverse);

                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                return str_replace(array_keys($reverse), array_values($reverse), $content);

            default:
                return $content;
        }
    }
}
