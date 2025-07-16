<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Georgian transliteration map
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_ka_GE
{
    public static $map = [
        // Mkhedruli + Capital Mkhedruli (Unicode 13+)
        'ა' => 'a', 'Ა' => 'A',
        'ბ' => 'b', 'Ბ' => 'B',
        'გ' => 'g', 'Გ' => 'G',
        'დ' => 'd', 'Დ' => 'D',
        'ე' => 'e', 'Ე' => 'E',
        'ვ' => 'v', 'Ვ' => 'V',
        'ზ' => 'z', 'Ზ' => 'Z',
        'თ' => 'th', 'Თ' => 'Th',
        'ი' => 'i', 'Ი' => 'I',
        'კ' => 'k', 'Კ' => 'K',
        'ლ' => 'l', 'Ლ' => 'L',
        'მ' => 'm', 'Მ' => 'M',
        'ნ' => 'n', 'Ნ' => 'N',
        'ო' => 'o', 'Ო' => 'O',
        'პ' => 'p', 'Პ' => 'P',
        'ჟ' => 'zh', 'Ჟ' => 'Zh',
        'რ' => 'r', 'Რ' => 'R',
        'ს' => 's', 'Ს' => 'S',
        'ტ' => 't', 'Ტ' => 'T',
        'უ' => 'u', 'Უ' => 'U',
        'ფ' => 'ph', 'Ფ' => 'Ph',
        'ქ' => 'q', 'Ქ' => 'Q',
        'ღ' => 'gh', 'Ღ' => 'Gh',
        'ყ' => 'qh', 'Ყ' => 'Qh',
        'შ' => 'sh', 'Შ' => 'Sh',
        'ჩ' => 'ch', 'Ჩ' => 'Ch',
        'ც' => 'ts', 'Ც' => 'Ts',
        'ძ' => 'dz', 'Ძ' => 'Dz',
        'წ' => 'w',  'Წ' => 'W',  // Differentiated to avoid conflict
        'ჭ' => 'tch', 'Ჭ' => 'Tch',
        'ხ' => 'kh', 'Ხ' => 'Kh',
        'ჯ' => 'j', 'Ჯ' => 'J',
        'ჰ' => 'h', 'Ჰ' => 'H',
    ];

    /**
     * Transliterate text between Georgian and Latin.
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

        $map = apply_filters('transliteration_map_ka_GE', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/ka_GE', [$map], '2.0.0', 'transliteration_map_ka_GE');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));

                // High-priority digraphs
                $custom = [
                    'Tch' => 'ჭ', 'tch' => 'ჭ',
                    'Dz' => 'ძ', 'dz' => 'ძ',
                    'Ts' => 'ც', 'ts' => 'ც',
                    'W' => 'წ', 'w' => 'წ', // differentiation
                    'Zh' => 'ჟ', 'zh' => 'ჟ',
                    'Kh' => 'ხ', 'kh' => 'ხ',
                    'Ph' => 'ფ', 'ph' => 'ფ',
                    'Gh' => 'ღ', 'gh' => 'ღ',
                    'Qh' => 'ყ', 'qh' => 'ყ',
                    'Th' => 'თ', 'th' => 'თ',
                    'Sh' => 'შ', 'sh' => 'შ',
                    'Ch' => 'ჩ', 'ch' => 'ჩ',
                ];

                $reverse = array_merge($custom, $reverse);

                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                return str_replace(array_keys($reverse), array_values($reverse), $content);

            default:
                return $content;
        }
    }
}
