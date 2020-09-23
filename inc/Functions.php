<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Set of important functions
 *
 * @link              http://infinitumform.com/
 * @since             1.0.7
 * @package           Serbian_Transliteration
 ***********************************************/

/*
 * Get current URL
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('get_current_url')) :
function get_current_url()
{
    return Serbian_Transliteration::__instance()->get_current_url();
}
endif;

/*
 * Get current script
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('get_script')) :
	function get_script()
	{
		return (isset($_COOKIE['rstr_script']) && in_array($_COOKIE['rstr_script'], apply_filters('rstr/allowed_script', array('cyr', 'lat')), true) !== false ? $_COOKIE['rstr_script'] : 'none');
	}
endif;

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

/*
 * Script selector
 * @return        HTML string/echo/object/array
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('script_selector')) :
	function script_selector ($args) {
		$url = get_current_url();
		$args = (object)wp_parse_args($args, array(
			'display_type' 	=> 'inline',
			'echo' 			=> false,
			'separator'     => ' | ',
			'cyr_caption'   => __('Cyrillic', RSTR_NAME),
			'lat_caption'   => Serbian_Transliteration::__instance()->cyr_to_lat(__('Latin', RSTR_NAME))
		));
		
		$options = (object)array(
			'active'	=> get_script(),
			'cyr'		=> add_query_arg('rstr', 'cyr', $url),
			'lat'		=> add_query_arg('rstr', 'lat', $url)
		);
		
		if(in_array($args->display_type, array('object', 'array'))) {
			$return = array();
		} else {
			$return = '';
		}
		
		switch($args->display_type)
		{
			default:
				$return = sprintf(__('Choose one of the display types: "%1$s", "%2$s", "%3$s", "%4$s" or "%5$s"', RSTR_NAME), 'inline', 'select', 'list', 'array', 'object');
				break;
			case 'inline':
				$return = sprintf(
					'<a href="%1$s" class="%6$s">%2$s</a>%3$s<a href="%4$s" class="%7$s">%5$s</a>',
					$options->lat,
					$args->lat_caption,
					$args->separator,
					$options->cyr,
					$args->cyr_caption,
					($options->active == 'lat' ? 'rstr-script-selector active' : 'rstr-script-selector'),
					($options->active == 'cyr' ? 'rstr-script-selector active' : 'rstr-script-selector')
				);
				break;
			case 'select':
				$return = '<script type="text/javascript">/*<![CDATA[*/function rstr_script_selector(redirect) {document.location.href = redirect.value;}/*]]>*/</script>';
				$return.= sprintf(
					'<select class="rstr-script-selector" onchange="rstr_script_selector(this);"><option value="%1$s"%5$s>%2$s</option><option value="%3$s"%6$s>%4$s</option></select>',
					$options->lat,
					$args->lat_caption,
					$options->cyr,
					$args->cyr_caption,
					($options->active == 'lat' ? ' selected' : ''),
					($options->active == 'cyr' ? ' selected' : '')
				);
				break;
			case 'list':
				$return = sprintf(
					'<ul class="rstr-script-selector"><li class="rstr-script-selector-item%5$s"><a href="%1$s" class="rstr-script-selector-item-link%5$s">%2$s</a></li><li class="rstr-script-selector-item%5$s"><a href="%3$s" class="rstr-script-selector-item-link%6$s">%4$s</a></li></ul>',
					$options->lat,
					$args->lat_caption,
					$options->cyr,
					$args->cyr_caption,
					($options->active == 'lat' ? ' active' : ''),
					($options->active == 'cyr' ? ' active' : '')
				);
				break;
			case 'array':
				$return = array(
					'cyr' => array(
						'caption' => $args->cyr_caption,
						'url' => $options->cyr,
					),
					'lat' => array(
						'caption' => $args->lat_caption,
						'url' => $options->lat,
					)
				);
				break;
			case 'object':
				$return = (object)array(
					'cyr' => (object)array(
						'caption' => $args->cyr_caption,
						'url' => $options->cyr,
					),
					'lat' => (object)array(
						'caption' => $args->lat_caption,
						'url' => $options->lat,
					)
				);
				break;
		}

		if($args->echo) {
			echo $return;
		} else {
			return $return;
		}
	}
endif;