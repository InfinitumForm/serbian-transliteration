<?php if ( !defined('WPINC') ) die();
/**
 * Active Theme: Divi
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Theme_Divi extends Transliteration
{
	function __construct(){
		$this->add_filter('transliteration_mode_filters', 'filters');
	} 
	
	public function filters ($filters=array()) {
		$filters = array_merge($filters, array(
			'et_before_main_content' => 'content',
			'et_after_main_content' => 'content',
			'et_before_content' => 'content',
			'et_html_top_header' => 'content',
			'et_html_slide_header' => 'content',
			'et_header_top' => 'content',
			'et_html_main_header' => 'content'
		));
		
		return $filters;
	}
}