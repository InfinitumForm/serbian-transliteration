<?php if ( !defined('WPINC') ) die();
/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Contact_Form_7 extends Transliteration
{
	function __construct(){
		$this->add_filter('transliteration_mode_filters', 'filters');
	} 
	
	public function filters ($filters=array()) {
		$filters = array_merge($filters, array(
			'wpcf7_display_message' => 'content',
			'wpcf7_default_template' => 'content',
			'wpcf7_form_response_output' => 'content',
			'wpcf7_ajax_json_echo' => 'content',
			'wpcf7_ajax_onload' => 'content',
			'wpcf7_contact_form_shortcode' => 'content',
			'wpcf7_flamingo_get_value' => 'content',
			'wpcf7_form_autocomplete' => 'content',
			'wpcf7_form_tag' => 'content',
			'wpcf7_messages' => 'content',
			'wpcf7_validation_error' => 'content',
		));
		
		return $filters;
	}
}