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

		if($this->is_editor())
		{}
		else
		{
			$this->add_shortcode('rstr_selector', 'rstr_selector_shortcode');
			$this->add_shortcode('rstr_cyr_to_lat', 'cyr_to_lat_shortcode');
			$this->add_shortcode('rstr_lat_to_cyr', 'lat_to_cyr_shortcode');
			$this->add_shortcode('rstr_img', 'img_shortcode');
			$this->add_shortcode('rstr_skip', 'skip_shortcode');
			$this->add_shortcode('transliteration', 'transliteration_shortcode');
			
			$this->add_action('wp_loaded', 'output_buffer_start', 99999);
			$this->add_action('shutdown', 'output_buffer_end', 99999);
		}
	}
	
	public function output_callback ($buffer='') {
		if(preg_match('/%{5}\-(\#{2}|\|{2})/', $buffer) !== false)
		{
			$buffer = preg_replace_callback('/(?<=%{5}\-\#{2})(.*?)(?=\#{2}\-%{5})/s', function($matches) {
				return $this->cyr_to_lat($matches[1]);
			}, $buffer);
			
			$buffer = preg_replace_callback('/(?<=%{5}\-\|{2})(.*?)(?=\|{2}\-%{5})/s', function($matches) {
				return $this->lat_to_cyr($matches[1]);
			}, $buffer);
			$buffer = str_replace(array('%%%%%-##','##-%%%%%','%%%%%-||','||-%%%%%'), '', $buffer);
		}
		return $buffer;
	}
	
	function output_buffer_start() { 
		ob_start(array(&$this, "output_callback"));
	}
	
	function output_buffer_end() { 
		ob_get_clean();
	}
	
	function rss_output_buffer_start() {
		ob_start();
	}
	
	public function rstr_selector_shortcode ($attr=array())
	{
		$args = (object)shortcode_atts(array(
			'type' 	=> 'inline',
			'separator'     => ' | ',
			'cyr_caption'   => __('Cyrillic', RSTR_NAME),
			'lat_caption'   => __('Latin', RSTR_NAME)
		), $attr, 'rstr_selector');
		
		return script_selector(array(
			'display_type' 	=> $args->type,
			'separator'     => $args->separator,
			'cyr_caption'   => $args->cyr_caption,
			'lat_caption'   => $args->lat_caption
		));
	}
	
	public function cyr_to_lat_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array('output' => 'shortcode'), $attr, 'rstr_cyr_to_lat' );
		
		switch($this->options['transliteration-mode'] ? $this->options['transliteration-mode'] : '')
		{
			default :
				return $content;
			break;
			
			case 'cyr_to_lat' :
			case 'lat_to_cyr' :
				if($attr->output == 'php'){
					return $this->cyr_to_lat(do_shortcode($content));
				} else {
					return '%%%%%-##' . $content . '##-%%%%%';
				}
			break;
		}
	}
	
	public function lat_to_cyr_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array('output' => 'shortcode'), $attr, 'rstr_lat_to_cyr' );
		
		switch($this->options['transliteration-mode'] ? $this->options['transliteration-mode'] : '')
		{
			default :
				return $content;
			break;
			
			case 'cyr_to_lat' :
			case 'lat_to_cyr' :
				if($attr->output == 'php'){
					return $this->lat_to_cyr(do_shortcode($content));
				} else {
					return '%%%%%-||' . $content . '||-%%%%%';
				}
			break;
		}
	}
	
	public function img_shortcode ($attr=array())
	{
		$attr = (object)shortcode_atts( array(
			'cyr' => NULL,
			'cyr_title'=>NULL,
			'cyr_caption'=>NULL,
			'lat' => NULL,
			'lat_title'=>NULL,
			'lat_caption'=>NULL,
			'default' => NULL,
			'default_title'=>NULL,
			'default_caption'=>NULL
		), $attr, 'rstr_img' );
		
		switch($this->get_current_script($this->options))
		{
			case 'lat':
			case 'cyr_to_lat':
				if($attr->lat_caption) {
					return sprintf('<figure><img src="%1$s" alt="%2$s"><figcaption>%3$s</figcaption></figure>', $attr->lat, $attr->lat_title, $attr->lat_caption);
				} else {
					return sprintf('<img src="%1$s" alt="%2$s">', $attr->lat, $attr->lat_title);
				}
			break;
				
			case 'cyr':
			case 'lat_to_cyr':
				if($attr->cyr_caption) {
					return sprintf('<figure><img src="%1$s" alt="%2$s"><figcaption>%3$s</figcaption></figure>', $attr->cyr, $attr->cyr_title, $attr->cyr_caption);
				} else {
					return sprintf('<img src="%1$s" alt="%2$s">', $attr->cyr, $attr->cyr_title);
				}
			break;
				
			default:
				if($attr->default_caption) {
					return sprintf('<figure><img src="%1$s" alt="%2$s"><figcaption>%3$s</figcaption></figure>', $attr->default, $attr->default_title, $attr->default_caption);
				} else {
					return sprintf('<img src="%1$s" alt="%2$s">', $attr->default, $attr->default_title);
				}
			break;
		}
	}
	
	public function transliteration_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array(
			'from' => 'cyr',
			'to' => 'lat'
		), $attr, 'transliteration' );
		
		if(in_array($attr->from, array('lat', 'cyr')) === false || in_array($attr->to, array('lat', 'cyr')) === false){
			return sprintf('<pre>%s</pre>', __('Transliteration shortcode does not have adequate parameters and translation is not possible. Please check the documentation.', RSTR_NAME));
		}
		
		switch(strtolower("{$attr->from}_to_{$attr->to}"))
		{
			case 'cyr_to_lat' :
				return $this->cyr_to_lat(do_shortcode($content));
			break;
				
			case 'lat_to_cyr' :
				return $this->lat_to_cyr(do_shortcode($content));
			break;
		}
		
		return $content;
	}
	
	public function skip_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array(), $attr, 'rstr_skip' );
		
		switch($this->options['transliteration-mode'] ? $this->options['transliteration-mode'] : '')
		{
			case 'cyr_to_lat' :
				return $this->lat_to_cyr_shortcode(array(), do_shortcode($content));
			break;
				
			case 'lat_to_cyr' :
				return $this->cyr_to_lat_shortcode(array(), do_shortcode($content));
			break;
		}
		
		return $content;
	}
}
endif;