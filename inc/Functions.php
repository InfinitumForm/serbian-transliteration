<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }

/*
 * Check is latin text
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_latin_text')) :
	function is_latin_text($content)
	{
		return Serbian_Transliteration::__instance()->is_lat($content);
	}
endif;

/*
 * Check is cyrillic text
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_cyrillic_text')) :
	function is_cyrillic_text($content)
	{
		return Serbian_Transliteration::__instance()->is_cyr($content);
	}
endif;

/*
 * Check is latin letters
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_latin')) :
	function is_latin($content)
	{
		return (isset($_COOKIE['rstr_script']) && $_COOKIE['rstr_script'] == 'lat');
	}
endif;

/*
 * Check is cyrillic letters
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_cyrillic')) :
	function is_cyrillic($content)
	{
		return (isset($_COOKIE['rstr_script']) && $_COOKIE['rstr_script'] == 'cyr');
	}
endif;

/*
 * Check is already cyrillic
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_already_cyrillic')) :
	function is_already_cyrillic()
	{
		return Serbian_Transliteration::__instance()->already_cyrillic();
	}
endif;

/*
 * Check is Serbian language
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_serbian')) :
	function is_serbian()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'sr_RS';
	}
endif;

/*
 * Check is Russian language
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_russian')) :
	function is_russian()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'ru_RU';
	}
endif;

/*
 * Check is Belarusian language
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_belarusian')) :
	function is_belarusian()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'bel';
	}
endif;

/*
 * Check is Bulgarian language
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_bulgarian')) :
	function is_bulgarian()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'bg_BG';
	}
endif;

/*
 * Check is Macedonian language
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_macedonian')) :
	function is_macedonian()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'mk_MK';
	}
endif;


/*
 * Check is Macedonian language
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('transliterate')) :
	function transliterate($content, $type='cyr_to_lat', $fix_html = true)
	{
		return Serbian_Transliteration::__instance()->transliterate_text($content, $type, $fix_html);
	}
endif;