<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Croatian language
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Croatian_Transliteration
 *
 */
class Transliteration_Map_hr
{
    public static $map = [
        // Composite digraphs
        'Ља' => 'Lja', 'ЉА' => 'LJA', 'ља' => 'lja',
        'Ље' => 'Lje', 'ЉЕ' => 'LJE', 'ље' => 'lje',
        'Љи' => 'Lji', 'ЉИ' => 'LJI', 'љи' => 'lji',
        'Љо' => 'Ljo', 'ЉО' => 'LJO', 'љо' => 'ljo',
        'Љу' => 'Lju', 'ЉУ' => 'LJU', 'љу' => 'lju',

        'Ња' => 'Nja', 'ЊА' => 'NJA', 'ња' => 'nja',
        'Ње' => 'Nje', 'ЊЕ' => 'NJE', 'ње' => 'nje',
        'Њи' => 'Nji', 'ЊИ' => 'NJI', 'њи' => 'nji',
        'Њо' => 'Njo', 'ЊО' => 'NJO', 'њо' => 'njo',
        'Њу' => 'Nju', 'ЊУ' => 'NJU', 'њу' => 'nju',

        'Џа' => 'Dža', 'ЏА' => 'DŽA', 'џа' => 'dža',
        'Џе' => 'Dže', 'ЏЕ' => 'DŽE', 'џе' => 'dže',
        'Џи' => 'Dži', 'ЏИ' => 'DŽI', 'џи' => 'dži',
        'Џо' => 'Džo', 'ЏО' => 'DŽO', 'џо' => 'džo',
        'Џу' => 'Džu', 'ЏУ' => 'DŽU', 'џу' => 'džu',

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

    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_hr', self::$map);

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip($map);

                $custom = [
                    'DŽ' => 'Џ', 'Dž' => 'Џ', 'dž' => 'џ',
                    'LJ' => 'Љ', 'Lj' => 'Љ', 'lj' => 'љ',
                    'NJ' => 'Њ', 'Nj' => 'Њ', 'nj' => 'њ',
                    'Đ'  => 'Ђ', 'đ'  => 'ђ',
                    'Č'  => 'Ч', 'č'  => 'ч',
                    'Ć'  => 'Ћ', 'ć'  => 'ћ',
                    'Š'  => 'Ш', 'š'  => 'ш',
                    'Ž'  => 'Ж', 'ž'  => 'ж',
                ];

                $reverse = array_merge($custom, $reverse);

                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                $escaped = array_map('preg_quote', array_keys($reverse));
                $pattern = '/' . implode('|', $escaped) . '/u';

                return preg_replace_callback($pattern, function ($match) use ($reverse) {
                    return $reverse[$match[0]] ?? $match[0];
                }, $content);

            default:
                return $content;
        }
    }
}
