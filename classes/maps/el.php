<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Greek (el) transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Transliteration_Map_el
{
    public static $map = [
        // Digraphs (handled first)
        'μπ' => 'b', 'ντ' => 'd', 'γκ' => 'g',
        'τσ' => 'ts', 'τζ' => 'dz',

        'Α' => 'A', 'α' => 'a', 'Ά' => 'Á', 'ά' => 'á',
        'Β' => 'V', 'β' => 'v',
        'Γ' => 'G', 'γ' => 'g',
        'Δ' => 'D', 'δ' => 'd',
        'Ε' => 'E', 'ε' => 'e', 'Έ' => 'É', 'έ' => 'é',
        'Ζ' => 'Z', 'ζ' => 'z',
        'Η' => 'I', 'η' => 'i', 'Ή' => 'Í', 'ή' => 'í',
        'Θ' => 'Th', 'θ' => 'th',
        'Ι' => 'I', 'ι' => 'i', 'Ί' => 'Í', 'ί' => 'í', 'ϊ' => 'ï', 'ΐ' => 'ḯ',
        'Κ' => 'K', 'κ' => 'k',
        'Λ' => 'L', 'λ' => 'l',
        'Μ' => 'M', 'μ' => 'm',
        'Ν' => 'N', 'ν' => 'n',
        'Ξ' => 'X', 'ξ' => 'x',
        'Ο' => 'O', 'ο' => 'o', 'Ό' => 'Ó', 'ό' => 'ó',
        'Π' => 'P', 'π' => 'p',
        'Ρ' => 'R', 'ρ' => 'r',
        'Σ' => 'S', 'σ' => 's', 'ς' => 's',
        'Τ' => 'T', 'τ' => 't',
        'Υ' => 'Y', 'υ' => 'y', 'Ύ' => 'Ý', 'ύ' => 'ý', 'ϋ' => 'ÿ', 'ΰ' => 'ÿ́',
        'Φ' => 'F', 'φ' => 'f',
        'Χ' => 'Ch', 'χ' => 'ch',
        'Ψ' => 'Ps', 'ψ' => 'ps',
        'Ω' => 'O', 'ω' => 'o', 'Ώ' => 'Ó', 'ώ' => 'ó',
    ];

    /**
     * Transliterate Greek to Latin or back.
     *
     * @param mixed $content
     * @param string $translation
     * @return mixed
     */
    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_el', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/el', [$map], '2.0.0', 'transliteration_map_el');

        switch ($translation) {
            case 'cyr_to_lat':
                // First handle digraphs manually
                $content = str_replace(['μπ', 'ντ', 'γκ', 'τσ', 'τζ'], ['b', 'd', 'g', 'ts', 'dz'], $content);
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_flip(array_filter($map, fn($v) => $v !== ''));
                $custom = [
                    'CH' => 'Χ', 'ch' => 'χ',
                    'TH' => 'Θ', 'th' => 'θ',
                    'PS' => 'Ψ', 'ps' => 'ψ',
                    'PH' => 'Φ', 'ph' => 'φ',
                    'KH' => 'Χ', 'kh' => 'χ',
                    'DZ' => 'ΤΖ', 'dz' => 'τζ',
                    'TS' => 'ΤΣ', 'ts' => 'τσ',
                    'NG' => 'ΓΚ', 'ng' => 'γκ',
                    'B' => 'ΜΠ', 'b' => 'μπ',
                    'D' => 'ΝΤ', 'd' => 'ντ',
                ];
                $reverse = array_merge($custom, $reverse);
                uksort($reverse, fn($a, $b) => strlen($b) <=> strlen($a));
                $reverse = apply_filters('rstr/inc/transliteration/el/lat_to_cyr', $reverse);
                return str_replace(array_keys($reverse), array_values($reverse), $content);
        }

        return $content;
    }
}
