<?php if ( !defined('WPINC') ) die();
/**
 * Armenian transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_hy {

	public static $map = array (
		// Variations and special characters
		'և'=>'ev',	'ու'=>'u',	'[\s\t]+?ո'=>'\svo',	
		'Ա'=>'A',	'Բ'=>'B',	'Գ'=>'G',	'Դ'=>'D',	'Ե'=>'Ye',	'Զ'=>'Z',	'Է'=>'E',	
		'Ը'=>'Eh',	'Թ'=>'Th',	'Ժ'=>'Zh',	'Ի'=>'I',	'Լ'=>'L',	'Խ'=>'X',	'Ծ'=>'Tc',	
		'Կ'=>'K',	'Հ'=>'H',	'Ձ'=>'Dz',	'Ղ'=>'Gh',	'Ճ'=>'Tch',	'Մ'=>'M',	'Յ'=>'Y',	
		'Ն'=>'N',	'Շ'=>'Sh',	'Ո'=>'Vo',	'Չ'=>'Ch',	'Պ'=>'P',	'Ջ'=>'J',	'Ռ'=>'R',	
		'Ս'=>'S',	'Վ'=>'V',	'Տ'=>'T',	'Ր'=>'R',	'Ց'=>'C',	'Փ'=>'Ph',	'Ք'=>'Kh',	
		'Օ'=>'O',	'Ֆ'=>'F',	
		'ա'=>'a',	'բ'=>'b',	'գ'=>'g',	'դ'=>'d',	'ե'=>'e',	'զ'=>'z',	'է'=>'e',	
		'ը'=>'eh',	'թ'=>'th',	'ժ'=>'zh',	'ի'=>'i',	'լ'=>'l',	'խ'=>'x',	'ծ'=>'tc',	
		'կ'=>'k',	'հ'=>'h',	'ձ'=>'dz',	'ղ'=>'gh',	'ճ'=>'tch',	'մ'=>'m',	'յ'=>'y',	
		'ն'=>'n',	'շ'=>'sh',	'ո'=>'o',	'չ'=>'ch',	'պ'=>'p',	'ջ'=>'j',	'ռ'=>'r',	
		'ս'=>'s',	'վ'=>'v',	'տ'=>'t',	'ր'=>'r',	'ց'=>'c',	'փ'=>'ph',	'ք'=>'kh',	
		'օ'=>'o',	'ֆ'=>'f',	
		'№'=>'#',	'—'=>'-',	'«'=>'',	'»'=>'',	'…'=>''
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_hy', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/hy', [$transliteration], '2.0.0', 'transliteration_map_hy');

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
				$transliteration = apply_filters('rstr/inc/transliteration/hy/lat_to_cyr', $transliteration);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}