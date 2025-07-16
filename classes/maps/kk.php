<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Kazakh transliteration (Cyrillic ↔ Latin)
 *
 * @link    https://infinitumform.com/
 * @since   2.0.0
 * @package Serbian_Transliteration
 */

class Transliteration_Map_kk
{
    public static $map = [
        // Complex digraphs
        'Ғ' => 'Ǵ',  'ғ' => 'ǵ',
        'Ё' => 'Yo', 'ё' => 'yo',
        'Ж' => 'Zh', 'ж' => 'zh',
        'Ң' => 'Ń',  'ң' => 'ń',
        'Х' => 'H',  'х' => 'h',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ю' => 'Iý', 'ю' => 'ıý',
        'Я' => 'Ia', 'я' => 'ia',

        // Base letters
        'А' => 'A',  'а' => 'a',
        'Б' => 'B',  'б' => 'b',
        'В' => 'V',  'в' => 'v',
        'Г' => 'G',  'г' => 'g',
        'Д' => 'D',  'д' => 'd',
        'Е' => 'E',  'е' => 'e',
        'З' => 'Z',  'з' => 'z',
        'И' => 'I',  'и' => 'i',
        'Й' => 'Ý',  'й' => 'ý',
        'К' => 'K',  'к' => 'k',
        'Л' => 'L',  'л' => 'l',
        'М' => 'M',  'м' => 'm',
        'Н' => 'N',  'н' => 'n',
        'О' => 'O',  'о' => 'o',
        'П' => 'P',  'п' => 'p',
        'Р' => 'R',  'р' => 'r',
        'С' => 'S',  'с' => 's',
        'Т' => 'T',  'т' => 't',
        'У' => 'Ý',  'у' => 'ý',
        'Ф' => 'F',  'ф' => 'f',
        'Қ' => 'Q',  'қ' => 'q',
        'Ң' => 'Ń',  'ң' => 'ń',
        'Ө' => 'Ó',  'ө' => 'ó',
        'Ұ' => 'U',  'ұ' => 'u',
        'Ү' => 'Ú',  'ү' => 'ú',
        'Һ' => 'H',  'һ' => 'h',
        'І' => 'I',  'і' => 'i',
        'Э' => 'E',  'э' => 'e',

        // Removed signs
        'Ъ' => '',   'ъ' => '',
        'Ь' => '',   'ь' => '',
    ];

    /**
     * Transliterate Kazakh text between Cyrillic and Latin.
     *
     * @param mixed  $content String to transliterate.
     * @param string $translation Direction of conversion.
     * @return mixed
     */
    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) {
            return $content;
        }

        $transliteration = apply_filters('transliteration_map_kk', self::$map);
        $transliteration = apply_filters_deprecated('rstr/inc/transliteration/kk', [$transliteration], '2.0.0', 'transliteration_map_kk');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $transliteration);

            case 'lat_to_cyr':
                $reverse = array_filter($transliteration, fn($v) => $v !== '');
                $reverse = array_flip($reverse);

                // Priority digraphs
                $manual = [
                    'Zh' => 'Ж', 'zh' => 'ж',
                    'Ch' => 'Ч', 'ch' => 'ч',
                    'Sh' => 'Ш', 'sh' => 'ш',
                    'Shch' => 'Щ', 'shch' => 'щ',
                    'Yo' => 'Ё', 'yo' => 'ё',
                    'Ia' => 'Я', 'ia' => 'я',
                    'Iý' => 'Ю', 'ıý' => 'ю',
                    'Ý'  => 'Й', 'ý' => 'й',
                    'Ó'  => 'Ө', 'ó' => 'ө',
                    'Ú'  => 'Ү', 'ú' => 'ү',
                    'Ǵ' => 'Ғ', 'ǵ' => 'ғ',
                    'Ń' => 'Ң', 'ń' => 'ң',
                ];

                $reverse = array_merge($manual, $reverse);
                $reverse = apply_filters('rstr/inc/transliteration/kk/lat_to_cyr', $reverse);

                return strtr($content, $reverse);
        }

        return $content;
    }
}
