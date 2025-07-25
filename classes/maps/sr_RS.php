<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Serbian language
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */
class Transliteration_Map_sr_RS
{
    public static $map = [
		// Composite digraphs with vowels
		'Ља' => 'Lja', 'ЉА' => 'LJA',
		'Ље' => 'Lje', 'ЉЕ' => 'LJE',
		'Љи' => 'Lji', 'ЉИ' => 'LJI',
		'Љо' => 'Ljo', 'ЉО' => 'LJO',
		'Љу' => 'Lju', 'ЉУ' => 'LJU',

		'Ња' => 'Nja', 'ЊА' => 'NJA',
		'Ње' => 'Nje', 'ЊЕ' => 'NJE',
		'Њи' => 'Nji', 'ЊИ' => 'NJI',
		'Њо' => 'Njo', 'ЊО' => 'NJO',
		'Њу' => 'Nju', 'ЊУ' => 'NJU',

		'Џа' => 'Dža', 'ЏА' => 'DŽA',
		'Џе' => 'Dže', 'ЏЕ' => 'DŽE',
		'Џи' => 'Dži', 'ЏИ' => 'DŽI',
		'Џо' => 'Džo', 'ЏО' => 'DŽO',
		'Џу' => 'Džu', 'ЏУ' => 'DŽU',

		'ља' => 'lja', 'ље' => 'lje', 'љи' => 'lji', 'љо' => 'ljo', 'љу' => 'lju',
		'ња' => 'nja', 'ње' => 'nje', 'њи' => 'nji', 'њо' => 'njo', 'њу' => 'nju',
		'џа' => 'dža', 'џе' => 'dže', 'џи' => 'dži', 'џо' => 'džo', 'џу' => 'džu',
		
		// Macedonian
		'Ѓ' => 'Ǵ', 'ѓ' => 'ǵ',
		'Ќ' => 'Ḱ', 'ќ' => 'ḱ',

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
		'Ѕ' => 'Dz', 'ѕ' => 'dz',
	];

    /**
     * Transliterate text between Cyrillic and Latin.
     *
     * @param mixed $content String to transliterate.
     * @param string $translation Conversion direction: 'cyr_to_lat' or 'lat_to_cyr'
     * @return mixed
     */
    public static function transliterate($content, $translation = 'cyr_to_lat')
    {
        if (!is_string($content)) {
            return $content;
        }

        $map = apply_filters('transliteration_map_sr_RS', self::$map);
        $map = apply_filters_deprecated('rstr/inc/transliteration/sr_RS', [$map], '2.0.0', 'transliteration_map_sr_RS');

        switch ($translation) {
            case 'cyr_to_lat':
                return strtr($content, $map);

            case 'lat_to_cyr':
				$content = ltrim($content, "\xEF\xBB\xBF\x20\t\n\r\0\x0B"); // UTF-8 BOM + whitespace
			
                // Build reverse map
                $reverse = array_flip($map);

                // Add digraph priority
                $custom = [
					'DZ' => 'Ѕ',
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

                // Sort descending by key length
                uksort($reverse, static fn($a, $b) => strlen($b) <=> strlen($a));

                $escaped = array_map('preg_quote', array_keys($reverse));
				$pattern = '/' . implode('|', $escaped) . '/u';

				$content = preg_replace_callback($pattern, function ($match) use ($reverse) {
					return $reverse[$match[0]] ?? $match[0];
				}, $content);

                // Handle specific known word issues
                $content = str_replace([
                    'оџљебња',
                    'ОЏЉЕБЊА',
                ], [
                    'оджљебња',
                    'ОДЖЉЕБЊА',
                ], $content);

                return $content;

            default:
                return $content;
        }
    }
}
