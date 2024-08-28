<?php if ( !defined('WPINC') ) die();
/**
 * Georgian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_ka_GE {

	public static $map = array (
		// Variations and special characters
		'ა' => 'a', 'Ა' => 'A', 'ბ' => 'b', 'Ბ' => 'B', 'გ' => 'g', 'Გ' => 'G',
		'დ' => 'd', 'Დ' => 'D', 'ე' => 'e', 'Ე' => 'E', 'ვ' => 'v', 'Ვ' => 'V',
		'ზ' => 'z', 'Ზ' => 'Z', 'თ' => 'th', 'Თ' => 'Th', 'ი' => 'i', 'Ი' => 'I',
		'კ' => 'k', 'Კ' => 'K', 'ლ' => 'l', 'Ლ' => 'L', 'მ' => 'm', 'Მ' => 'M',
		'ნ' => 'n', 'Ნ' => 'N', 'ო' => 'o', 'Ო' => 'O', 'პ' => 'p', 'Პ' => 'P',
		'ჟ' => 'zh', 'Ჟ' => 'Zh', 'რ' => 'r', 'Რ' => 'R', 'ს' => 's', 'Ს' => 'S',
		'ტ' => 't', 'Ტ' => 'T', 'უ' => 'u', 'Უ' => 'U', 'ფ' => 'ph', 'Ფ' => 'Ph',
		'ქ' => 'q', 'Ქ' => 'Q', 'ღ' => 'gh', 'Ღ' => 'Gh', 'ყ' => 'qh', 'Ყ' => 'Qh',
		'შ' => 'sh', 'Შ' => 'Sh', 'ჩ' => 'ch', 'Ჩ' => 'Ch', 'ც' => 'ts', 'Ც' => 'Ts',
		'ძ' => 'dz', 'Ძ' => 'Dz', 'წ' => 'ts', 'Წ' => 'Ts', 'ჭ' => 'tch', 'Ჭ' => 'Tch',
		'ხ' => 'kh', 'Ხ' => 'Kh', 'ჯ' => 'j', 'Ჯ' => 'J', 'ჰ' => 'h', 'Ჰ' => 'H'
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_ka_GE', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/ka_GE', [$transliteration], '2.0.0', 'transliteration_map_ka_GE');

		switch($translation)
		{
			case 'cyr_to_lat' :
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_filter($transliteration, function($t){
					return $t != '';
				});
				$transliteration = array_flip($transliteration);
				$transliteration = apply_filters('rstr/inc/transliteration/ka_GE/lat_to_cyr', $transliteration);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}