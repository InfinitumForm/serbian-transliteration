<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Macedonian transliteration map
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_mk_MK
{
    /**
     * Cyrillic to Latin map for Macedonian
     */
    public static $map = [
        // Digraphs and special letters
        'Ѓ' => 'Ǵ',  'ѓ' => 'ǵ',
		'Ќ' => 'Ḱ',  'ќ' => 'ḱ',
        'Љ' => 'Lj', 'љ' => 'lj',
        'Њ' => 'Nj', 'њ' => 'nj',
        'Ѕ' => 'Dz', 'ѕ' => 'dz',
        'Ж' => 'Zh', 'ж' => 'zh',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Џ' => 'Dž', 'џ' => 'dž',

        // Standard letters
        'А' => 'A',  'а' => 'a',
        'Б' => 'B',  'б' => 'b',
        'В' => 'V',  'в' => 'v',
        'Г' => 'G',  'г' => 'g',
        'Д' => 'D',  'д' => 'd',
        'Е' => 'E',  'е' => 'e',
        'З' => 'Z',  'з' => 'z',
        'И' => 'I',  'и' => 'i',
        'Ј' => 'J',  'ј' => 'j',
        'К' => 'K',  'к' => 'k',
        'Л' => 'L',  'л' => 'l',
        'М' => 'M',  'м' => 'm',
        'Н' => 'N',  'н' => 'n',
        'О' => 'O',  'о' => 'o',
        'П' => 'P',  'п' => 'p',
        'Р' => 'R',  'р' => 'r',
        'С' => 'S',  'с' => 's',
        'Т' => 'T',  'т' => 't',
        'У' => 'U',  'у' => 'u',
        'Ф' => 'F',  'ф' => 'f',
        'Х' => 'H',  'х' => 'h',
        'Ъ' => 'Ǎ',  'ъ' => 'ǎ', // Optional legacy transliteration
    ];

    /**
     * Transliterate text between Cyrillic and Latin.
     *
     * @param mixed $content String to transliterate.
     * @param string $translation Direction: 'cyr_to_lat' or 'lat_to_cyr'
     * @return mixed
     */
    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_mk_MK', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/mk_MK', [$map], '2.0.0', 'transliteration_map_mk_MK');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));

                // Manual overrides for priority digraphs
                $custom = [
                    'Dž' => 'Џ', 'dž' => 'џ',
                    'Dz' => 'Ѕ', 'dz' => 'ѕ',
                    'Gj' => 'Ѓ', 'gj' => 'ѓ',
                    'Kj' => 'Ќ', 'kj' => 'ќ',
                    'Lj' => 'Љ', 'lj' => 'љ',
                    'Nj' => 'Њ', 'nj' => 'њ',
                    'Zh' => 'Ж', 'zh' => 'ж',
                    'Sh' => 'Ш', 'sh' => 'ш',
                    'Ch' => 'Ч', 'ch' => 'ч',
                ];

                $reverse = array_merge($custom, $reverse);

                // Sort digraphs first
                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                $content = str_replace(array_keys($reverse), array_values($reverse), $content);

                return apply_filters('rstr/inc/transliteration/mk_MK/lat_to_cyr', $content);

            default:
                return $content;
        }
    }
}
