<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Armenian transliteration map
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_hy
{
    public static $map = [
        // Common ligatures and combinations
        'և' => 'ev',  // Armenian ligature (e + v)
        'ու' => 'u',   // Common digraph

        // Capital letters
        'Ա' => 'A', 'Բ' => 'B', 'Գ' => 'G', 'Դ' => 'D', 'Ե' => 'Ye', 'Զ' => 'Z', 'Է' => 'E',
        'Ը' => 'Eh', 'Թ' => 'Th', 'Ժ' => 'Zh', 'Ի' => 'I', 'Լ' => 'L', 'Խ' => 'X', 'Ծ' => 'Tc',
        'Կ' => 'K', 'Հ' => 'H', 'Ձ' => 'Dz', 'Ղ' => 'Gh', 'Ճ' => 'Tch', 'Մ' => 'M', 'Յ' => 'Y',
        'Ն' => 'N', 'Շ' => 'Sh', 'Ո' => 'O', 'Չ' => 'Ch', 'Պ' => 'P', 'Ջ' => 'J', 'Ռ' => 'R',
        'Ս' => 'S', 'Վ' => 'V', 'Տ' => 'T', 'Ր' => 'R', 'Ց' => 'C', 'Փ' => 'Ph', 'Ք' => 'Kh',
        'Օ' => 'O', 'Ֆ' => 'F',

        // Lowercase letters
        'ա' => 'a', 'բ' => 'b', 'գ' => 'g', 'դ' => 'd', 'ե' => 'e', 'զ' => 'z', 'է' => 'e',
        'ը' => 'eh', 'թ' => 'th', 'ժ' => 'zh', 'ի' => 'i', 'լ' => 'l', 'խ' => 'x', 'ծ' => 'tc',
        'կ' => 'k', 'հ' => 'h', 'ձ' => 'dz', 'ղ' => 'gh', 'ճ' => 'tch', 'մ' => 'm', 'յ' => 'y',
        'ն' => 'n', 'շ' => 'sh', 'ո' => 'o', 'չ' => 'ch', 'պ' => 'p', 'ջ' => 'j', 'ռ' => 'r',
        'ս' => 's', 'վ' => 'v', 'տ' => 't', 'ր' => 'r', 'ց' => 'c', 'փ' => 'ph', 'ք' => 'kh',
        'օ' => 'o', 'ֆ' => 'f',

        // Symbols
        '№' => '#', '—' => '-', '«' => '', '»' => '', '…' => '',
    ];

    /**
     * Transliterate text between Armenian and Latin.
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

        $map = apply_filters('transliteration_map_hy', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/hy', [$map], '2.0.0', 'transliteration_map_hy');

        switch ($translation) {
            case 'cyr_to_lat':
                // Special: replace initial "Ո" (U+0548) with "Vo", only at beginning of word
                $content = preg_replace_callback('/\bՈ/u', function () {
                    return 'Vo';
                }, $content);
                $content = preg_replace_callback('/\bո/u', function () {
                    return 'vo';
                }, $content);

                return strtr($content, $map);

            case 'lat_to_cyr':
                // Build reverse map
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));

                // Custom high-priority digraphs
                $custom = [
                    'Dž' => 'Ջ', 'dz' => 'ձ',
                    'Zh' => 'Ժ', 'zh' => 'ժ',
                    'Ch' => 'Չ', 'ch' => 'չ',
                    'Sh' => 'Շ', 'sh' => 'շ',
                    'Gh' => 'Ղ', 'gh' => 'ղ',
                    'Tch' => 'Ճ', 'tch' => 'ճ',
                    'Tc' => 'Ծ', 'tc' => 'ծ',
                    'Th' => 'Թ', 'th' => 'թ',
                    'Ph' => 'Փ', 'ph' => 'փ',
                    'Kh' => 'Ք', 'kh' => 'ք',
                    'Eh' => 'Ը', 'eh' => 'ը',
                    'Ye' => 'Ե', 'ye' => 'ե',
                    'Vo' => 'Ո', 'vo' => 'ո',
                    'ev' => 'և', // ligature
                    'u'  => 'ու', // note: may overfire without context
                ];

                $reverse = array_merge($custom, $reverse);

                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                return str_replace(array_keys($reverse), array_values($reverse), $content);

            default:
                return $content;
        }
    }
}
