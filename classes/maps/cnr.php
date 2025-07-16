<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Montenegrin transliteration map
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_cnr
{
    public static $map = [
        // Crnogorska posebna slova
        'С́' => 'Ś', 'с́' => 'ś',
        'З́' => 'Ź', 'з́' => 'ź',

        // Composite digraphs
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

        // Standard Cyrillic to Latin
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

        $map = apply_filters('transliteration_map_cnr', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/cnr', [$map], '2.0.0', 'transliteration_map_cnr');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));

                // Digraph priority
                $custom = [
                    'Dž' => 'Џ', 'dž' => 'џ',
                    'Lj' => 'Љ', 'lj' => 'љ',
                    'Nj' => 'Њ', 'nj' => 'њ',
                    'Ś' => 'С́', 'ś' => 'с́',
                    'Ź' => 'З́', 'ź' => 'з́',
                    'Đ'  => 'Ђ', 'đ' => 'ђ',
                    'Č'  => 'Ч', 'č' => 'ч',
                    'Ć'  => 'Ћ', 'ć' => 'ћ',
                    'Š'  => 'Ш', 'š' => 'ш',
                    'Ž'  => 'Ж', 'ž' => 'ж',
                ];

                $reverse = array_merge($custom, $reverse);

                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                return str_replace(array_keys($reverse), array_values($reverse), $content);

            default:
                return $content;
        }
    }
}
