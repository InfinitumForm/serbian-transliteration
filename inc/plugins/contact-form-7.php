<?php if ( !defined('WPINC') ) die();
/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Plugin__contact_form_7')) :
	class Serbian_Transliteration__Plugin__contact_form_7 extends Serbian_Transliteration
	{
		
		/* Run this script */
		public static function run($dry = false) {
			$class = self::class;
			$instance = Serbian_Transliteration_Cache::get($class);
			if ( !$instance ) {
				$instance = Serbian_Transliteration_Cache::set($class, new self($dry));
			}
			return $instance;
		}
		
		function __construct($dry = false){
			if($dry) return;
			$this->add_filter('rstr/transliteration/exclude/filters', array(get_class(), 'filters'));
		} 
		
		public static function filters ($filters=array()) {
			
			$classname = self::run(true);
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
endif;