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
	$parse_url = Serbian_Transliteration_Utilities::parse_url();
	return $parse_url['url'];
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
		return Serbian_Transliteration_Utilities::get_current_script();
	}
elseif(!function_exists('rstr_get_script')):
	function rstr_get_script()
	{
		return Serbian_Transliteration_Utilities::get_current_script();
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
	function is_latin()
	{
		if(function_exists('get_script')){
			$script = get_script();
		} else {
			$script = rstr_get_script();
		}
		return ($script == 'cyr_to_lat');
	}
endif;

/*
 * Check is cyrillic letters
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_cyrillic')) :
	function is_cyrillic()
	{
		if(function_exists('get_script')){
			$script = get_script();
		} else {
			$script = rstr_get_script();
		}
		return ($script == 'lat_to_cyr');
	}
endif;

/*
 * Check is already cyrillic
 * @return        boolean
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
 * @return        boolean
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
 * @return        boolean
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
 * @return        boolean
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
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_macedonian')) :
	function is_macedonian()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'mk_MK';
	}
endif;

/*
 * Check is Kazakh language
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_kazakh')) :
	function is_kazakh()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'kk';
	}
endif;

/*
 * Greece (Elini'ka) transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_greece')) :
	function is_greece()
	{
		return Serbian_Transliteration::__instance()->get_locale() == 'el';
	}
endif;

/*
 * Elini'ka is natural Greece language, alias of function is_greece()
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_elinika')) :
	function is_elinika()
	{
		return is_greece();
	}
endif;

/*
 * Transliterate content
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
 * Translate from Cyrillic to Latin
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('cyr_to_lat')) :
	function cyr_to_lat($content)
	{
		return Serbian_Transliteration::__instance()->cyr_to_lat($content);
	}
endif;

/*
 * Translate from Latin to Cyrillic
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('lat_to_cyr')) :
	function lat_to_cyr($content, $fix_html = true)
	{
		return Serbian_Transliteration::__instance()->lat_to_cyr($content, $fix_html);
	}
endif;

/*
 * Script selector
 * @return        HTML string/echo/object/array
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('script_selector')) :
	function script_selector ($args) {
		$parse_url = Serbian_Transliteration_Utilities::parse_url();
		$url = $parse_url['url'];
		
		$ID = uniqid('script_selector_');
		
		$args = (object)wp_parse_args($args, array(
			'id'			=> $ID,
			'display_type' 	=> 'inline',
			'echo' 			=> false,
			'separator'     => ' | ',
			'cyr_caption'   => __('Cyrillic', RSTR_NAME),
			'lat_caption'   => Serbian_Transliteration::__instance()->cyr_to_lat(__('Latin', RSTR_NAME))
		));
		
		$options = (object)array(
			'active'	=> get_script(),
			'cyr'		=> add_query_arg(get_rstr_option('url-selector', 'rstr'), 'cyr', $url),
			'lat'		=> add_query_arg(get_rstr_option('url-selector', 'rstr'), 'lat', $url)
		);
		
		if(in_array($args->display_type, array('object', 'array'))) {
			$return = array();
		} else {
			$return = '';
		}
		
		switch($args->display_type)
		{
			default:
				$return = sprintf(__('Choose one of the display types: "%1$s", "%2$s", "%3$s", "%4$s", "%5$s" or "%6$s"', RSTR_NAME), 'inline', 'select', 'list', 'list_items', 'array', 'object');
				break;
			case 'inline':
				$return = sprintf(
					apply_filters(
						'rstr/inc/functions/script_selector/inline',
						'<a href="%1$s" class="rstr-script-selector%6$s">%2$s</a>%3$s<a href="%4$s" class="rstr-script-selector%7$s">%5$s</a>',
						$args,
						$options
					),
					$options->lat,
					$args->lat_caption,
					$args->separator,
					$options->cyr,
					$args->cyr_caption,
					($options->active == 'lat' ? ' active' : ' inactive'),
					($options->active == 'cyr' ? ' active' : ' inactive')
				);
				break;
			case 'select':
				$return = '<script type="text/javascript">/*<![CDATA[*/function rstr_' . $ID . '(redirect) {document.location.href = redirect.value;}/*]]>*/</script>';
				$return.= sprintf(
					apply_filters(
						'rstr/inc/functions/script_selector/select',
						'<select class="rstr-script-selector" onchange="rstr_' . $ID . '(this);" id="rstr_' . $ID . '"><option value="%1$s"%5$s>%2$s</option><option value="%3$s"%6$s>%4$s</option></select>',
						$args,
						$options
					),
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
					apply_filters(
						'rstr/inc/functions/script_selector/list', '<ul class="rstr-script-selector" id="rstr_' . $ID . '"><li class="rstr-script-selector-item%5$s"><a href="%1$s" class="rstr-script-selector-item-link%5$s">%2$s</a></li><li class="rstr-script-selector-item%6$s"><a href="%3$s" class="rstr-script-selector-item-link%6$s">%4$s</a></li></ul>',
						$args,
						$options
					),
					$options->lat,
					$args->lat_caption,
					$options->cyr,
					$args->cyr_caption,
					($options->active == 'lat' ? ' active' : ' inactive'),
					($options->active == 'cyr' ? ' active' : ' inactive')
				);
				break;
			case 'list_items':
				$return = sprintf(
					apply_filters(
						'rstr/inc/functions/script_selector/list_items',
						'<li class="rstr-script-selector-item%5$s"><a href="%1$s" class="rstr-script-selector-item-link%5$s">%2$s</a></li><li class="rstr-script-selector-item%6$s"><a href="%3$s" class="rstr-script-selector-item-link%6$s">%4$s</a></li>',
						$args,
						$options
					),
					$options->lat,
					$args->lat_caption,
					$options->cyr,
					$args->cyr_caption,
					($options->active == 'lat' ? ' active' : ' inactive'),
					($options->active == 'cyr' ? ' active' : ' inactive')
				);
				break;
			case 'array':
				$return = apply_filters('rstr/inc/functions/script_selector/array', array(
					'cyr' => array(
						'caption' => $args->cyr_caption,
						'url' => $options->cyr,
					),
					'lat' => array(
						'caption' => $args->lat_caption,
						'url' => $options->lat,
					)
				), $args, $options);
				break;
			case 'object':
				$return = $return = apply_filters('rstr/inc/functions/script_selector/object', (object)array(
					'cyr' => (object)array(
						'caption' => $args->cyr_caption,
						'url' => $options->cyr,
					),
					'lat' => (object)array(
						'caption' => $args->lat_caption,
						'url' => $options->lat,
					)
				), $args, $options);
				break;
		}

		if($args->echo) {
			echo $return;
		} else {
			return $return;
		}
	}
endif;