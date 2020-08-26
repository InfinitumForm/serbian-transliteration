<?php
/**
 * Shortcodes
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
class Serbian_Transliteration_Shortcodes extends Serbian_Transliteration
{
	private $options;
	
	function __construct($options){
		$this->options = $options;
		
		$this->add_shortcode('rstr_cyr_to_lat', 'cyr_to_lat');
		$this->add_shortcode('rstr_lat_to_cyr', 'lat_to_cyr');
		$this->add_shortcode('transliteration', 'transliteration');
	}
	
	public function cyr_to_lat ($attr=array(), $content='')
	{
		return str_replace($this->cyr, $this->lat, do_shortcode($content));
	}
	
	public function lat_to_cyr ($attr=array(), $content='')
	{
		$content = str_replace($this->lat, $this->cyr, do_shortcode($content));
		return $this->fix_html($content);
	}
	
	public function transliteration ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array(
			'from' => 'cyr',
			'to' => 'lat'
		), $attr, 'transliteration' );
		
		switch(strtolower("{$attr->from}_to_{$attr->to}"))
		{
			case 'cyr_to_lat' :
				return str_replace($this->cyr, $this->lat, do_shortcode($content));
				break;
				
			case 'lat_to_cyr' :
				$content = str_replace($this->lat, $this->cyr, do_shortcode($content));
				return $this->fix_html($content);				
				break;
		}
		
		return sprintf('<pre>%s</pre>', __('Transliteration shortcode does not have adequate parameters and translation is not possible. Please check the documentation.', RSTR_NAME));
		
	}
}