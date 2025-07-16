<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Kyrgyz (Kyrgyzstan) Transliteration (Cyrillic ↔ Latin)
 *
 * @link    https://infinitumform.com/
 * @since   2.0.0
 * @package Serbian_Transliteration
 */

class Transliteration_Map_kir
{
    public static $map = [
        // Digraphs
        'Ё' => 'Yo', 'ё' => 'yo',
        'Ж' => 'Zh', 'ж' => 'zh',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',

        // Core letters
        'А' => 'A',  'а' => 'a',
        'Б' => 'B',  'б' => 'b',
        'В' => 'V',  'в' => 'v',
        'Г' => 'G',  'г' => 'g',
        'Д' => 'D',  'д' => 'd',
        'Е' => 'E',  'е' => 'e',
        'З' => 'Z',  'з' => 'z',
        'И' => 'I',  'и' => 'i',
        'Й' => 'Y',  'й' => 'y',
        'К' => 'K',  'к' => 'k',
        'Л' => 'L',  'л' => 'l',
        'М' => 'M',  'м' => 'm',
        'Н' => 'N',  'н' => 'n',
        'О' => 'O',  'о' => 'o',
        'Ө' => 'Ö',  'ө' => 'ö',
        'П' => 'P',  'п' => 'p',
        'Р' => 'R',  'р' => 'r',
        'С' => 'S',  'с' => 's',
        'Т' => 'T',  'т' => 't',
        'У' => 'U',  'у' => 'u',
        'Ү' => 'Ü',  'ү' => 'ü',
        'Ф' => 'F',  'ф' => 'f',
        'Х' => 'H',  'х' => 'h',
        'Ы' => 'Y',  'ы' => 'y',
        'Э' => 'E',  'э' => 'e',
        'Ъ' => 'ʼ',  'ъ' => 'ʼ',
        'Ь' => 'ʼ',  'ь' => 'ʼ',
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

        $map = apply_filters('transliteration_map_kir', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/kir', [$map], '2.0.0', 'transliteration_map_kir');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                // Flip and prioritize digraphs
                $reverse = array_filter($map, static fn($v) => $v !== '');
                $priority = [
                    'Shch' => 'Щ', 'shch' => 'щ',
                    'Zh'   => 'Ж', 'zh'   => 'ж',
                    'Ts'   => 'Ц', 'ts'   => 'ц',
                    'Ch'   => 'Ч', 'ch'   => 'ч',
                    'Sh'   => 'Ш', 'sh'   => 'ш',
                    'Yo'   => 'Ё', 'yo'   => 'ё',
                    'Yu'   => 'Ю', 'yu'   => 'ю',
                    'Ya'   => 'Я', 'ya'   => 'я',
                    'Y'    => 'Й', 'y'    => 'й',
                ];
                $reverse = array_merge($priority, array_flip($reverse));
                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                return strtr($content, $reverse);

            default:
                return $content;
        }
    }
}
