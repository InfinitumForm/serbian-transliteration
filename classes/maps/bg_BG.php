<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Bulgarian (bg_BG) transliteration map
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_bg_BG
{
    public static $map = [
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Й' => 'J', 'й' => 'j',
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
        'Х' => 'H', 'х' => 'h',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Sht', 'щ' => 'sht',
        'Ъ' => 'A',  'ъ' => 'a', // ISO 9: Ǎ / ǎ
        'Ь' => '',   'ь' => '',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',
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

        $map = apply_filters('transliteration_map_bg_BG', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/bg_BG', [$map], '2.0.0', 'transliteration_map_bg_BG');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));
                $custom = [
                    'Sht' => 'Щ', 'sht' => 'щ',
                    'Zh' => 'Ж', 'zh' => 'ж',
                    'Ts' => 'Ц', 'ts' => 'ц',
                    'Ch' => 'Ч', 'ch' => 'ч',
                    'Sh' => 'Ш', 'sh' => 'ш',
                    'Yu' => 'Ю', 'yu' => 'ю',
                    'Ya' => 'Я', 'ya' => 'я',
                ];
                $reverse = array_merge($custom, $reverse);
                uksort($reverse, fn($a, $b) => strlen($b) <=> strlen($a));
                return str_replace(array_keys($reverse), array_values($reverse), $content);

            default:
                return $content;
        }
    }
}
