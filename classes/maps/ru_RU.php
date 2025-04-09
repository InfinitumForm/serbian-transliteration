<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Russian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */

class Transliteration_Map_ru_RU
{
    public static $map = [
        // Variations and special characters
        'Ё' => 'Yo', 'Ж' => 'Zh', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Shch', 'Ю' => 'Ju', 'Я' => 'Ja',
        'ё' => 'yo', 'ж' => 'zh', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ю' => 'ju', 'я' => 'ja',

        // All other letters
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K',
        'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P',
        'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
        'Ъ' => '',  'Ы' => 'Y', 'Ь' => '',  'Э' => 'E',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
        'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
        'ъ' => '',  'ы' => 'y', 'ь' => '',  'э' => 'e',
    ];

    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_ru_RU', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/ru_RU', [$map], '2.0.0', 'transliteration_map_ru_RU');

        switch ($translation) {
            case 'cyr_to_lat':
                // Special rules for word-initial letters
                $content = preg_replace('/\bЁ/u', 'Yo', $content);
                $content = preg_replace('/\bё/u', 'yo', $content);
                $content = preg_replace('/\bЮ/u', 'Yu', $content);
                $content = preg_replace('/\bю/u', 'yu', $content);
                $content = preg_replace('/\bЯ/u', 'Ya', $content);
                $content = preg_replace('/\bя/u', 'ya', $content);
                $content = preg_replace('/\bЙ/u', 'Y', $content);
                $content = preg_replace('/\bй/u', 'y', $content);

                return strtr($content, $map);

            case 'lat_to_cyr':
                // Reverse map with extended replacements
                $reverse = array_filter($map, static fn($v) => $v !== '');
                $reverse = array_merge([
                    'SHCH' => 'Щ', 'Shch' => 'Щ', 'shch' => 'щ',
                    'ZH'   => 'Ж', 'Zh'   => 'Ж', 'zh'   => 'ж',
                    'KH'   => 'Х', 'Kh'   => 'Х', 'kh'   => 'х',
                    'TS'   => 'Ц', 'Ts'   => 'Ц', 'ts'   => 'ц',
                    'CH'   => 'Ч', 'Ch'   => 'Ч', 'ch'   => 'ч',
                    'SH'   => 'Ш', 'Sh'   => 'Ш', 'sh'   => 'ш',
                    'YO'   => 'Ё', 'Yo'   => 'Ё', 'yo'   => 'ё',
                    'JU'   => 'Ю', 'Ju'   => 'Ю', 'ju'   => 'ю',
                    'JA'   => 'Я', 'Ja'   => 'Я', 'ja'   => 'я',
                    'Y'    => 'Й', 'y'    => 'й',
                ], array_flip($reverse));

                // Prioritize longer sequences
                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                $output = $content;

                // Handle initial letter logic (reversed)
                $output = preg_replace('/\bYo/u', 'Ё', $output);
                $output = preg_replace('/\byo/u', 'ё', $output);
                $output = preg_replace('/\bYu/u', 'Ю', $output);
                $output = preg_replace('/\byu/u', 'ю', $output);
                $output = preg_replace('/\bYa/u', 'Я', $output);
                $output = preg_replace('/\bya/u', 'я', $output);
                $output = preg_replace('/\bY/u', 'Й', $output);
                $output = preg_replace('/\by/u', 'й', $output);

                foreach ($reverse as $latin => $cyrillic) {
                    $output = str_replace($latin, $cyrillic, $output);
                }

                return apply_filters('rstr/inc/transliteration/ru_RU/lat_to_cyr', $output);

            default:
                return $content;
        }
    }
}
