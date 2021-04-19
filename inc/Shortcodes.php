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
	function __construct(){

		if(Serbian_Transliteration_Utilities::is_editor()) {} else
		{
			// Shortcodes
			$this->add_shortcode('rstr_selector', 'rstr_selector_shortcode');
			$this->add_shortcode('rstr_cyr_to_lat', 'cyr_to_lat_shortcode');
			$this->add_shortcode('rstr_lat_to_cyr', 'lat_to_cyr_shortcode');
			$this->add_shortcode('rstr_img', 'img_shortcode');
			$this->add_shortcode('rstr_skip', 'skip_shortcode');
			$this->add_shortcode('transliteration', 'transliteration_shortcode');
			// Buffer
			$this->add_action('wp_loaded', 'output_buffer_start', (PHP_INT_MAX-1));
			$this->add_action('shutdown', 'output_buffer_end', (PHP_INT_MAX-1));
		}
	}
	
	
	/*
	 * Script selector
	 */
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
	
	/*
	 * Cyrillic to Latin
	 */
	public function cyr_to_lat_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array('output' => 'shortcode'), $attr, 'rstr_cyr_to_lat' );
		
		switch($this->get_option('transliteration-mode', ''))
		{
			default :
				return $content;
			break;
			
			case 'cyr_to_lat' :
			case 'lat_to_cyr' :
				if($attr->output == 'php'){
					return $this->cyr_to_lat(do_shortcode($content));
				} else {
					return '%%%%%-##' . do_shortcode($content) . '##-%%%%%';
				}
			break;
		}
	}
	
	/*
	 * Latin to Cyrillic
	 */
	public function lat_to_cyr_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts(array(
			'output' => 'shortcode',
			'fix_html' => true,
			'fix_diacritics' => false
		), $attr, 'rstr_lat_to_cyr' );
		
		$attr->fix_html = ($attr->fix_html == 1 || $attr->fix_html === true || $attr->fix_html == 'true') === true;
		$attr->fix_diacritics = ($attr->fix_diacritics == 1 || $attr->fix_diacritics === true || $attr->fix_diacritics == 'true') === true;
		
		switch($this->get_option('transliteration-mode', ''))
		{
			default :
				return $content;
			break;
			
			case 'cyr_to_lat' :
			case 'lat_to_cyr' :
				if($attr->output == 'php'){
					return $this->lat_to_cyr(do_shortcode($content), $attr->fix_html, $attr->fix_diacritics);
				} else {
					return '%%%%%-||' . do_shortcode($content) . '||-%%%%%';
				}
			break;
		}
	}
	
	/*
	 * Image shortcode
	 */
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
		
		switch(Serbian_Transliteration_Utilities::get_current_script())
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
	
	/*
	 * Transliteration
	 */
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
				return '%%%%%-##' . $this->cyr_to_lat(do_shortcode($content)) . '##-%%%%%';
			break;
				
			case 'lat_to_cyr' :
				return '%%%%%-||' . $this->lat_to_cyr(do_shortcode($content)) . '||-%%%%%';
			break;
		}
		
		return $content;
	}
	
	/*
	 * Skip transliteration
	 */
	public function skip_shortcode ($attr=array(), $content='')
	{
		$attr = (object)shortcode_atts( array(), $attr, 'rstr_skip' );
		
		switch($this->get_option('transliteration-mode', ''))
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
	
	
	/*
	 * Shortcode buffer
	 */
	public function output_callback ($buffer='') {
		if(preg_match('/%{5}\-(\#{2}|\|{2})/', $buffer) !== false)
		{
			$buffer = preg_replace_callback('/(?<=%{5}\-\#{2})(.*?)(?=\#{2}\-%{5})/s', function($matches) {
				return $this->cyr_to_lat($matches[1]);
			}, $buffer);
			$buffer = preg_replace_callback('/(?<=%{5}\-\|{2})(.*?)(?=\|{2}\-%{5})/s', function($matches) {
				return $this->lat_to_cyr($matches[1]);
			}, $buffer);
			
			
			$buffer = preg_replace_callback('/(?<=\{lat_to_cyr\})(.*?)(?=\{\/lat_to_cyr\})/s', function($matches) {
				return $this->lat_to_cyr($matches[1]);
			}, $buffer);
			$buffer = preg_replace_callback('/(?<=\{cyr_to_lat\})(.*?)(?=\{\/cyr_to_lat\})/s', function($matches) {
				return $this->cyr_to_lat($matches[1]);
			}, $buffer);
			$buffer = preg_replace_callback('/(?<=\{rstr_skip\})(.*?)(?=\{\/rstr_skip\})/s', function($matches) {
				switch($this->get_option('transliteration-mode', ''))
				{
					case 'cyr_to_lat' :
						return $this->lat_to_cyr($matches[1]);
					break;
						
					case 'lat_to_cyr' :
						return $this->cyr_to_lat($matches[1]);
					break;
				}
				return $matches[1];
			}, $buffer);
			
			$buffer = str_replace(array(
				'%%%%%-##',
				'##-%%%%%',
				'%%%%%-||',
				'||-%%%%%',
				'{cyr_to_lat}',
				'{/cyr_to_lat}',
				'{lat_to_cyr}',
				'{/lat_to_cyr}',
				'{rstr_skip}',
				'{/rstr_skip}'
			), '', $buffer);
		}
		return $buffer;
	}
	
	function output_buffer_start() { 
		ob_start(array(&$this, "output_callback"));
	}
	
	function output_buffer_end() { 
		ob_get_clean();
	}
	
}
endif;