<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Bosnian transliteration map (based on Serbian)
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_bs_BA
{
    public static $map = [
        // Composite digraphs with vowels
        'Ља' => 'Lja', 'Ље' => 'Lje', 'Љи' => 'Lji', 'Љо' => 'Ljo', 'Љу' => 'Lju',
        'Ња' => 'Nja', 'Ње' => 'Nje', 'Њи' => 'Nji', 'Њо' => 'Njo', 'Њу' => 'Nju',
        'Џа' => 'Dža', 'Џе' => 'Dže', 'Џи' => 'Dži', 'Џо' => 'Džo', 'Џу' => 'Džu',

        'ља' => 'lja', 'ље' => 'lje', 'љи' => 'lji', 'љо' => 'ljo', 'љу' => 'lju',
        'ња' => 'nja', 'ње' => 'nje', 'њи' => 'nji', 'њо' => 'njo', 'њу' => 'nju',
        'џа' => 'dža', 'џе' => 'dže', 'џи' => 'dži', 'џо' => 'džo', 'џу' => 'džu',

        // Single digraphs
        'Љ' => 'Lj', 'љ' => 'lj',
        'Њ' => 'Nj', 'њ' => 'nj',
        'Џ' => 'Dž', 'џ' => 'dž',

        // Standard Cyrillic to Latin mapping
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Ђ' => 'Đ', 'ђ' => 'đ',
        'Е' => 'E', 'е' => 'e',
        'Ж' => 'Ž', 'ж' => 'ž',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Ј' => 'J', 'ј' => 'j',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'Ћ' => 'Ć', 'ћ' => 'ć',
        'У' => 'U', 'у' => 'u',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'H', 'х' => 'h',
        'Ц' => 'C', 'ц' => 'c',
        'Ч' => 'Č', 'ч' => 'č',
        'Ш' => 'Š', 'ш' => 'š',
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

        $map = apply_filters('transliteration_map_bs_BA', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/bs_BA', [$map], '2.0.0', 'transliteration_map_bs_BA');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip($map);

                // Ensure correct digraphs first
                $custom = [
                    'Dž' => 'Џ', 'dž' => 'џ',
                    'Lj' => 'Љ', 'lj' => 'љ',
                    'Nj' => 'Њ', 'nj' => 'њ',
                    'Đ'  => 'Ђ', 'đ' => 'ђ',
                    'Č'  => 'Ч', 'č' => 'ч',
                    'Ć'  => 'Ћ', 'ć' => 'ћ',
                    'Š'  => 'Ш', 'š' => 'š',
                    'Ž'  => 'Ж', 'ž' => 'ž',
                ];

                $reverse = array_merge($custom, $reverse);

                // Sort descending by key length
                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                $content = str_replace(array_keys($reverse), array_values($reverse), $content);

                return apply_filters('rstr/inc/transliteration/bs_BA/lat_to_cyr', $content);

            default:
                return $content;
        }
    }
}
