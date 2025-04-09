<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Ukrainian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */

class Transliteration_Map_uk
{
    public static $map = [
        // Special variations
        'ЗГ' => 'ZGH', 'Зг' => 'Zgh', 'зг' => 'zgh',

        ' Є' => ' Ye', ' є' => ' ye',
        ' Ї' => ' Yi', ' ї' => ' yi', ' Й' => ' Y', ' й' => ' y',
        ' Ю' => ' Yu', ' ю' => ' yu', ' Я' => ' Ya', ' я' => ' ya',

        // Variations and special characters
        'Є' => 'Ie', 'є' => 'ie', 'Ї' => 'i', 'ї' => 'i', 'Щ' => 'Shch',
        'щ' => 'shch', 'Ю' => 'Iu', 'ю' => 'iu', 'Я' => 'Ia', 'я' => 'ia',

        // All other letters
        'А' => 'A', 'а' => 'a', 'Б' => 'B', 'б' => 'b', 'В' => 'V',
        'в' => 'v', 'Г' => 'H', 'г' => 'h', 'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e', 'Ж' => 'Zh', 'ж' => 'zh', 'З' => 'Z',
        'з' => 'z', 'И' => 'Y', 'и' => 'y', 'І' => 'I', 'і' => 'i',
        'Й' => 'J', 'й' => 'j', 'К' => 'K', 'к' => 'k', 'Л' => 'L',
        'л' => 'l', 'М' => 'M', 'м' => 'm', 'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o', 'П' => 'P', 'п' => 'p', 'Р' => 'R',
        'р' => 'r', 'С' => 'S', 'с' => 's', 'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u', 'Ф' => 'F', 'ф' => 'f', 'Х' => 'Kh',
        'х' => 'kh', 'Ц' => 'Ts', 'ц' => 'ts', 'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh', 'Ґ' => 'G', 'ґ' => 'g', 'Ь' => '',
        'ь' => '', "'" => '',
    ];

    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_uk', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/uk', [$map], '2.0.0', 'transliteration_map_uk');

        switch ($translation) {
            case 'cyr_to_lat':
				// Handle special case when certain letters appear at the beginning of a word
				$content = preg_replace('/\bЄ/u', 'Ye', $content);
				$content = preg_replace('/\bє/u', 'ye', $content);
				$content = preg_replace('/\bЇ/u', 'Yi', $content);
				$content = preg_replace('/\bї/u', 'yi', $content);
				$content = preg_replace('/\bЮ/u', 'Yu', $content);
				$content = preg_replace('/\bю/u', 'yu', $content);
				$content = preg_replace('/\bЯ/u', 'Ya', $content);
				$content = preg_replace('/\bя/u', 'ya', $content);
				$content = preg_replace('/\bЙ/u', 'Y', $content);
				$content = preg_replace('/\bй/u', 'y', $content);
                return strtr($content, $map);

            case 'lat_to_cyr':
                $reverse = array_filter($map, static fn($v) => $v !== '');
                $reverse = array_merge([
                    'ZGH' => 'ЗГ', 'Zgh' => 'Зг', 'zgh' => 'зг',
                    'SHCH' => 'Щ', 'Shch' => 'Щ', 'shch' => 'щ',
                    'YE' => 'Є', 'Ye' => 'Є', 'ye' => 'є',
                    'YI' => 'Ї', 'Yi' => 'Ї', 'yi' => 'ї',
                    'YU' => 'Ю', 'Yu' => 'Ю', 'yu' => 'ю',
                    'IA' => 'Я', 'Ia' => 'Я', 'ia' => 'я',
                    'IE' => 'Є', 'Ie' => 'Є', 'ie' => 'є',
                    'IU' => 'Ю', 'Iu' => 'Ю', 'iu' => 'ю',
                    'YA' => 'Я', 'Ya' => 'Я', 'ya' => 'я',
                    'KH' => 'Х', 'Kh' => 'Х', 'kh' => 'х',
                    'TS' => 'Ц', 'Ts' => 'Ц', 'ts' => 'ц',
                    'CH' => 'Ч', 'Ch' => 'Ч', 'ch' => 'ч',
                    'SH' => 'Ш', 'Sh' => 'Ш', 'sh' => 'ш',
                    'ZH' => 'Ж', 'Zh' => 'Ж', 'zh' => 'ж',
                ], array_flip($reverse));

                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a)); // prioritize longer keys

				// Handle special cases for letters at the beginning of words
				$content = preg_replace('/\bYe/u', 'Є', $content);
				$content = preg_replace('/\bye/u', 'є', $content);
				$content = preg_replace('/\bYi/u', 'Ї', $content);
				$content = preg_replace('/\byi/u', 'ї', $content);
				$content = preg_replace('/\bYu/u', 'Ю', $content);
				$content = preg_replace('/\byu/u', 'ю', $content);
				$content = preg_replace('/\bYa/u', 'Я', $content);
				$content = preg_replace('/\bya/u', 'я', $content);
				$content = preg_replace('/\bY/u', 'Й', $content);
				$content = preg_replace('/\by/u', 'й', $content);

                $output = $content;
                foreach ($reverse as $latin => $cyrillic) {
                    $output = str_replace($latin, $cyrillic, $output);
                }

                return apply_filters('rstr/inc/transliteration/uk/lat_to_cyr', $output);

            default:
                return $content;
        }
    }
}
