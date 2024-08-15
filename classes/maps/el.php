<?php if ( !defined('WPINC') ) die();
/**
 * Greece (Elini'ka) transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 */

class Transliteration_Map_el {

	public static $map = array (
		'Χ' => 'Ch',	'χ' => 'ch',	'Ψ' => 'Ps',	'ψ' => 'ps',
		'τζ' => 'dz',	'τσ' => 'ts',	'γκ' => 'ng',


		'Α' => 'A',	'α' => 'a',	'Β' => 'V',	'β' => 'v',
		'Γ' => 'G',	'γ' => 'g',	'Δ' => 'D',	'δ' => 'd',
		'Ε' => 'E',	'ε' => 'e',	'Ζ' => 'Z',	'ζ' => 'z',
		'Η' => 'I',	'η' => 'i',	'Θ' => 'T',	'θ' => 't',
		'Ι' => 'I',	'ι' => 'i',	'Κ' => 'K',	'κ' => 'k',
		'Λ' => 'L',	'λ' => 'l',	'Μ' => 'M',	'μ' => 'm',
		'Ν' => 'N',	'ν' => 'n',	'Ξ' => 'X',	'ξ' => 'x',
		'Ο' => 'O',	'ο' => 'o',	'Π' => 'P',	'π' => 'p',
		'Ρ' => 'R',	'ρ' => 'r',

		'Σ' => 'S',	'σ' => 's',	'ς' => 's', // All is sigma

		'Τ' => 'T',	'τ' => 't',	'Υ' => 'Y',	'υ' => 'y',
		'Φ' => 'F',	'φ' => 'f',	'Ω' => 'O',	'ω' => 'o',

		'μπ' => 'b',	'ντ' => 'd',
	);

	public static function transliterate ($content, $translation = 'cyr_to_lat')
	{
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;

		$transliteration = apply_filters('transliteration_map_el', self::$map);
		$transliteration = apply_filters_deprecated('rstr/inc/transliteration/el', [$transliteration], '2.0.0', 'transliteration_map_el');

		switch($translation)
		{
			case 'cyr_to_lat' :
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;

			case 'lat_to_cyr' :
				$transliteration = array_flip($transliteration);

				$transliteration = array_merge(array(
					'CH' => 'Χ', 'PS' => 'Ψ', 'KH' => 'Χ', 'Kh' => 'Χ', 'kh' => 'χ', 'th' => 'θ',
					'RH' => 'Ρ', 'Rh' => 'Ρ', 'rh' => 'ρ', 'TH' => 'Θ', 'Th' => 'Θ', 'Ē' => 'Η',
					'ē' => 'η', 'PI' => 'Π', 'Pi' => 'Π', 'pi' => 'π', 'af' => 'αυ', 'ef' => 'ευ',
					'if' => 'ηυ', 'AI' => 'ΑΙ', 'Ai' => 'ΑΙ', 'ai' => 'αι'
				), $transliteration);

				$transliteration = apply_filters('rstr/inc/transliteration/el/lat_to_cyr', $transliteration);
			//	return str_replace(array_keys($transliteration), array_values($transliteration), $content);
				return strtr($content, $transliteration);
				break;
		}

		return $content;
	}
}