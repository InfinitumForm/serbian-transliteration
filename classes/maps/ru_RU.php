<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Russian transliteration (Cyrillic ↔ Latin)
 *
 * @link    https://infinitumform.com/
 * @since   2.0.0
 * @package Serbian_Transliteration
 */

class Transliteration_Map_ru_RU
{
    public static $map = [
        // Digraphs
        'Ё' => 'Yo', 'ё' => 'yo',
        'Ж' => 'Zh', 'ж' => 'zh',
        'Х' => 'Kh', 'х' => 'kh',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',

        // Base letters
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
        'П' => 'P',  'п' => 'p',
        'Р' => 'R',  'р' => 'r',
        'С' => 'S',  'с' => 's',
        'Т' => 'T',  'т' => 't',
        'У' => 'U',  'у' => 'u',
        'Ф' => 'F',  'ф' => 'f',
        'Ы' => 'Y',  'ы' => 'y',
        'Э' => 'E',  'э' => 'e',

        // Removed signs
        'Ъ' => '',   'ъ' => '',
        'Ь' => '',   'ь' => '',
    ];

    /**
     * Transliterate Russian text between Cyrillic and Latin.
     *
     * @param mixed  $content String to transliterate.
     * @param string $translation Direction of conversion.
     * @return mixed
     */
    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_ru_RU', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/ru_RU', [$map], '2.0.0', 'transliteration_map_ru_RU');

        switch ($translation) {
            case 'cyr_to_lat':
                // Transliterate using map
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_filter($map, static fn($v) => $v !== '');
                $reverse = array_flip($reverse);

                // Prioritized digraphs
                $priority = [
                    'Shch' => 'Щ', 'shch' => 'щ',
                    'Zh'   => 'Ж', 'zh'   => 'ж',
                    'Kh'   => 'Х', 'kh'   => 'х',
                    'Ts'   => 'Ц', 'ts'   => 'ц',
                    'Ch'   => 'Ч', 'ch'   => 'ч',
                    'Sh'   => 'Ш', 'sh'   => 'ш',
                    'Yo'   => 'Ё', 'yo'   => 'ё',
                    'Yu'   => 'Ю', 'yu'   => 'ю',
                    'Ya'   => 'Я', 'ya'   => 'я',
                    'Y'    => 'Й', 'y'    => 'й',
                ];

                // Combine and sort for replacement priority
                $reverse = array_merge($priority, $reverse);
                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                $content = strtr($content, $reverse);

                return apply_filters('rstr/inc/transliteration/ru_RU/lat_to_cyr', $content);

            default:
                return $content;
        }
    }
}
