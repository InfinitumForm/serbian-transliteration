<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Set of important functions
 *
 * @link              http://infinitumform.com/
 * @since             1.0.7
 * @package           Serbian_Transliteration
 ***********************************************/

// Indicate functions file for the extended plugins
if(!defined('RSTR_FUNCTIONS')) define('RSTR_FUNCTIONS', true);


/*
 * Is transliteration excluded
 * @return        bool
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('transliteration_excluded')) :
	function transliteration_excluded() : bool
	{
		return Serbian_Transliteration_Utilities::exclude_transliteration();
	}
endif;

/*
 * Get the latin URL
 * @param         $url (optional)
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('get_latin_url')) :
	function get_latin_url(string $url = NULL)
	{
		
		if( $url ) {
			return add_query_arg( get_rstr_option('url-selector', 'rstr'), 'lat', $url );
		}
		
		return add_query_arg( get_rstr_option('url-selector', 'rstr'), 'lat' );
	}
endif;


/*
 * Get the cyrillic URL
 * @param         $url (optional)
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('get_cyrillic_url')) :
	function get_cyrillic_url(string $url = NULL)
	{
		if( $url ) {
			return add_query_arg( get_rstr_option('url-selector', 'rstr'), 'cyr', $url );
		}
		
		return add_query_arg( get_rstr_option('url-selector', 'rstr'), 'cyr' );
	}
endif;



/*
 * Get the active transliteration
 * @return        string
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('get_active_transliteration')) :
	function get_active_transliteration()
	{
		return (function_exists('rstr_get_script') ? rstr_get_script() : get_script());
	}
endif;


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
		_doing_it_wrong( 'rstr_get_script', __('This function is deprecated and will be removed. Replace it with the `get_script()` function', 'serbian-transliteration'), '1.10.5' );
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
		return Serbian_Transliteration_Utilities::is_lat($content);
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
		return Serbian_Transliteration_Utilities::is_cyr($content);
	}
endif;

/*
 * Check is site on latin
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
		return in_array($script, array('cyr_to_lat', 'lat'));
	}
endif;

/*
 * Check is site on cyrillic
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
		return in_array($script, array('lat_to_cyr', 'cyr'));
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
		return Serbian_Transliteration_Utilities::already_cyrillic();
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
		return Serbian_Transliteration_Utilities::get_locale('sr_RS');
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
		return Serbian_Transliteration_Utilities::get_locale('ru_RU');
	}
endif;

/*
 * Check is Georgian language
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_georgian')) :
	function is_georgian()
	{
		return Serbian_Transliteration_Utilities::get_locale('ka_GE');
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
		return Serbian_Transliteration_Utilities::get_locale('bel');
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
		return Serbian_Transliteration_Utilities::get_locale('bg_BG');
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
		return Serbian_Transliteration_Utilities::get_locale('mk_MK');
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
		return Serbian_Transliteration_Utilities::get_locale('kk');
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
		return Serbian_Transliteration_Utilities::get_locale('el');
	}
endif;

/*
 * Arabic transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_arabic')) :
	function is_arabic()
	{
		return Serbian_Transliteration_Utilities::get_locale('ar');
	}
endif;

/*
 * Armenian transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_armenian')) :
	function is_armenian()
	{
		return Serbian_Transliteration_Utilities::get_locale('hy');
	}
endif;

/*
 * Mongolian transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_mongolian')) :
	function is_mongolian()
	{
		return Serbian_Transliteration_Utilities::get_locale('mn');
	}
endif;

/*
 * Bashkir transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_bashkir')) :
	function is_bashkir()
	{
		return Serbian_Transliteration_Utilities::get_locale('ba');
	}
endif;

/*
 * Uzbek transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_uzbek')) :
	function is_uzbek()
	{
		return Serbian_Transliteration_Utilities::get_locale('uz_UZ');
	}
endif;

/*
 * Kyrgyz transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_kyrgyz')) :
	function is_kyrgyz()
	{
		return Serbian_Transliteration_Utilities::get_locale('kir');
	}
endif;
/*
 * Tajik transliteration
 * @return        boolean
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('is_tajik')) :
	function is_tajik()
	{
		return Serbian_Transliteration_Utilities::get_locale('tg');
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
	function transliterate($content, string $type='cyr_to_lat', bool $fix_html = true)
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
	function lat_to_cyr($content, bool $fix_html = true)
	{
		return Serbian_Transliteration::__instance()->lat_to_cyr($content, $fix_html);
	}
endif;


/*
 * Script selector
 * @return        HTML string/echo/object/array
 * @author        Ivijan-Stefan Stipic
*/
if (!function_exists('script_selector')) :
    function script_selector(array $args = []) {
        $ID = uniqid('script_selector_');

        $defaults = [
            'id'          => $ID,
            'display_type' => 'inline',
            'echo'         => false,
            'separator'    => ' | ',
            'cyr_caption'  => __('Cyrillic', 'serbian-transliteration'),
            'lat_caption'  => __('Latin', 'serbian-transliteration')
        ];
        $args = (object) wp_parse_args($args, $defaults);

        $options = (object) [
            'active' => get_active_transliteration(),
            'cyr'    => get_cyrillic_url(),
            'lat'    => get_latin_url()
        ];

        $templateData = [
            'lat_url'      => esc_url($options->lat),
            'lat_caption'  => '{cyr_to_lat}' . esc_html($args->lat_caption) . '{/cyr_to_lat}',
            'cyr_url'      => esc_url($options->cyr),
            'cyr_caption'  => esc_html($args->cyr_caption),
            'active_lat'   => in_array($options->active, ['cyr_to_lat', 'lat']) ? ' active' : ' inactive',
            'active_cyr'   => in_array($options->active, ['lat_to_cyr', 'cyr']) ? ' active' : ' inactive',
            'separator'    => $args->separator,
            'ID'           => esc_attr($ID)
        ];

        $templateHandlers = [
            'inline' => function ($data) {
                return sprintf('<a href="%1$s" class="rstr-script-selector%6$s">%2$s</a>%3$s<a href="%4$s" class="rstr-script-selector%7$s">%5$s</a>',
                    $data['lat_url'], $data['lat_caption'], $data['separator'], $data['cyr_url'], $data['cyr_caption'], $data['active_lat'], $data['active_cyr']);
            },
            'select' => function ($data) {
                $script = '<script type="text/javascript">/*<![CDATA[*/function rstr_' . $data['ID'] . '(redirect) {document.location.href = redirect.value;}/*]]>*/</script>';
                return $script . sprintf('<select class="rstr-script-selector" onchange="rstr_%1$s(this);" id="rstr_%1$s"><option value="%2$s"%6$s>%3$s</option><option value="%4$s"%7$s>%5$s</option></select>',
                    $data['ID'], $data['lat_url'], $data['lat_caption'], $data['cyr_url'], $data['cyr_caption'], $data['active_lat'], $data['active_cyr']);
            },
            // Ostale display_type opcije...
        ];

        $return = isset($templateHandlers[$args->display_type]) ? 
            apply_filters("rstr/inc/functions/script_selector/{$args->display_type}", call_user_func($templateHandlers[$args->display_type], $templateData), $args, $options) : 
            sprintf(__('Choose one of the display types: "%1$s", "%2$s", "%3$s", "%4$s", "%5$s" or "%6$s"', 'serbian-transliteration'), 'inline', 'select', 'list', 'list_items', 'array', 'object');

        if ($args->echo) {
            echo $return;
        } else {
            return $return;
        }
    }
endif;



/*
 * INTERNAL FUNCTION: Execute Transliteration Buffer
 * @author        Ivijan-Stefan Stipic
*/
if(!function_exists('rstr_execute_transliteration_buffer')) :
function rstr_execute_transliteration_buffer()
{
	if(class_exists(Serbian_Transliteration_Utilities::mode(), false)) {
		$class = Serbian_Transliteration_Utilities::mode();
		if( method_exists($class, 'execute_buffer') ) {
			$class::execute_buffer();
		}
	}
}
endif;