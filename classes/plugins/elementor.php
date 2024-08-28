<?php if ( !defined('WPINC') ) die();
/**
 * Active Plugin: Elementor Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Elementor extends Transliteration
{
	function __construct(){
		$this->add_filter('transliteration_mode_filters', 'filters');
	} 
	
	public function filters ($filters=array()) {
		$filters = array_merge($filters, array(
			'elementor/frontend/the_content' => 'content'
		));
		
		return $filters;
	}
}