<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Shortcodes
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Shortcodes')) :
class Serbian_Transliteration_Shortcodes extends Serbian_Transliteration
{
	private $options;
	
	function __construct($options){
		$this->options = $options;
		
		$this->add_shortcode('rstr_cyr_to_lat', 'cyr_to_lat_shortcode');
		$this->add_shortcode('rstr_lat_to_cyr', 'lat_to_cyr_shortcode');
		$this->add_shortcode('transliteration', 'transliteration_shortcode');
	}
	
	public function cyr_to_lat_shortcode ($attr=array(), $content='')
	{
		return $this->cyr_to_lat(do_shortcode($content));
	}
	
	public function lat_to_cyr_shortcode ($attr=array(), $content='')
	{
		return $this->lat_to_cyr(do_shortcode($content));
	}
	
	public function transliteration_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array(
			'from' => 'cyr',
			'to' => 'lat'
		), $attr, 'transliteration' );
		
		switch(strtolower("{$attr->from}_to_{$attr->to}"))
		{
			case 'cyr_to_lat' :
				return $this->cyr_to_lat(do_shortcode($content));
				break;
				
			case 'lat_to_cyr' :
				return $this->lat_to_cyr(do_shortcode($content));
				break;
		}
		
		return sprintf('<pre>%s</pre>', __('Transliteration shortcode does not have adequate parameters and translation is not possible. Please check the documentation.', RSTR_NAME));
		
	}
}
endif;