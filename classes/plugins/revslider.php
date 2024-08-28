<?php if ( !defined('WPINC') ) die();
/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Revslider extends Transliteration
{
	function __construct(){
		$this->add_filter('transliteration_mode_filters', 'filters');
	} 
	
	public function filters ($filters=array()) {
		$filters = array_merge($filters, array(
			'revslider_add_static_layer_html' => 'content',
			'revslider_mod_stream_meta' => 'content',
			'revslider_add_layer_html' => 'content'
		));
		
		return $filters;
	}
}