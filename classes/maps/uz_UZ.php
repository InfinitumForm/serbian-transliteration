<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Uzbek (uz_UZ) transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.12.1
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_uz_UZ
{
    public static $map = [
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ф' => 'F', 'ф' => 'f',
        'Г' => 'G', 'г' => 'g',
        'Ғ' => 'G‘', 'ғ' => 'g‘',
        'Ҳ' => 'H', 'ҳ' => 'h',
        'И' => 'I', 'и' => 'i',
        'Й' => 'Y', 'й' => 'y',
        'Ж' => 'Zh', 'ж' => 'zh',
        'К' => 'K', 'к' => 'k',
        'Қ' => 'Q', 'қ' => 'q',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'Ў' => 'O‘', 'ў' => 'o‘',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'В' => 'V', 'в' => 'v',
        'Х' => 'X', 'х' => 'x',
        'Ц' => 'Ts', 'ц' => 'ts',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ъ' => 'ʼ', 'ъ' => 'ʼ',
        'Ь' => 'ʼ', 'ь' => 'ʼ',
        'Э' => 'E', 'э' => 'e',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',
        'З' => 'Z', 'з' => 'z',
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

        $map = apply_filters('transliteration_map_uz_UZ', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/uz_UZ', [$map], '2.0.0', 'transliteration_map_uz_UZ');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));
                $custom = [
                    'Shch' => 'Щ', 'shch' => 'щ',
                    'G‘' => 'Ғ', 'g‘' => 'ғ',
                    'O‘' => 'Ў', 'o‘' => 'ў',
                    'Zh' => 'Ж', 'zh' => 'ж',
                    'Ch' => 'Ч', 'ch' => 'ч',
                    'Sh' => 'Ш', 'sh' => 'ш',
                    'Ts' => 'Ц', 'ts' => 'ц',
                    'Yu' => 'Ю', 'yu' => 'ю',
                    'Ya' => 'Я', 'ya' => 'я',
                    'X' => 'Х', 'x' => 'х',
                ];
                $reverse = array_merge($custom, $reverse);
                uksort($reverse, fn($a, $b) => strlen($b) <=> strlen($a));
                $reverse = apply_filters('rstr/inc/transliteration/uz_UZ/lat_to_cyr', $reverse);
                return str_replace(array_keys($reverse), array_values($reverse), $content);

            default:
                return $content;
        }
    }
}
