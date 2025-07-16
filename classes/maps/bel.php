<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Belarusian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             2.0.0
 * @package           Serbian_Transliteration
 */

class Transliteration_Map_bel
{
    public static $map = [
        // Complex digraphs – uppercase first for priority in strtr
        'Дж' => 'Dž', 'Дз' => 'Dz', 'Сь' => 'Ś',
        'дж' => 'dž', 'дз' => 'dz', 'сь' => 'ś',

        // Vowel mutations at word/boundary level handled in regex
        'Ё' => 'Jo', 'ё' => 'jo',
        'Ю' => 'Ju', 'ю' => 'ju',
        'Я' => 'Ja', 'я' => 'ja',
        'Е' => 'Je', 'е' => 'je',

        // Main alphabet
        'А' => 'A',  'а' => 'a',
        'Б' => 'B',  'б' => 'b',
        'В' => 'V',  'в' => 'v',
        'Г' => 'H',  'г' => 'h',
        'Д' => 'D',  'д' => 'd',
        'Ж' => 'Ž',  'ж' => 'ž',
        'З' => 'Z',  'з' => 'z',
        'І' => 'I',  'і' => 'i',
        'Й' => 'J',  'й' => 'j',
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
        'Ў' => 'Ŭ',  'ў' => 'ŭ',
        'Ф' => 'F',  'ф' => 'f',
        'Х' => 'Ch', 'х' => 'ch',
        'Ц' => 'C',  'ц' => 'c',
        'Ч' => 'Č',  'ч' => 'č',
        'Ш' => 'Š',  'ш' => 'š',
        'Ы' => 'Y',  'ы' => 'y',
        'Э' => 'E',  'э' => 'e',
        'Ь' => '',   'ь' => "'",
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

        $transliteration = apply_filters('transliteration_map_bel', self::$map);
        $transliteration = apply_filters_deprecated('rstr/inc/transliteration/bel', [$transliteration], '2.0.0', 'transliteration_map_bel');

        switch ($translation) {
            case 'cyr_to_lat':
                // Word-initial softening for Je/Jo/Ju/Ja (handled as je/jo/ju/ja)
                $content = preg_replace_callback('/(?<=^|\s|[АаЕеІіОоУуЫыЭэЪъЬь])([ЕеЁёЮюЯя])/', function ($m) {
                    $map = ['Е' => 'Je', 'е' => 'je', 'Ё' => 'Jo', 'ё' => 'jo', 'Ю' => 'Ju', 'ю' => 'ju', 'Я' => 'Ja', 'я' => 'ja'];
                    return $map[$m[1]] ?? $m[1];
                }, $content);
                return strtr($content, $transliteration);

            case 'lat_to_cyr':
                // Flip and filter
                $reverse = array_filter($transliteration, fn($v) => $v !== '');
                $reverse = array_flip($reverse);

                // Override long digraphs (case sensitive)
                $manual = [
                    'Dž' => 'Дж', 'dž' => 'дж',
                    'Dz' => 'Дз', 'dz' => 'дз',
                    'Je' => 'Е',  'je' => 'е',
                    'Jo' => 'Ё',  'jo' => 'ё',
                    'Ju' => 'Ю',  'ju' => 'ю',
                    'Ja' => 'Я',  'ja' => 'я',
                    'Ś'  => 'Сь', 'ś' => 'сь',
                    'Ŭ'  => 'Ў',  'ŭ' => 'ў',
                    'Ch' => 'Х',  'ch' => 'х',
                    'Č'  => 'Ч',  'č' => 'ч',
                    'Š'  => 'Ш',  'š' => 'ш',
                    'Ž'  => 'Ж',  'ž' => 'ж',
                    'Č'  => 'Ч',  'č' => 'ч',
                ];

                $reverse = array_merge($manual, $reverse);
                $reverse = apply_filters('rstr/inc/transliteration/bel/lat_to_cyr', $reverse);

                return strtr($content, $reverse);
        }

        return $content;
    }
}
