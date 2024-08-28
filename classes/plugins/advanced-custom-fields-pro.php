<?php if ( !defined('WPINC') ) die();
/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Advanced_Custom_Fields_Pro extends Transliteration
{
	function __construct(){
		$this->add_filter('transliteration_mode_filters', 'filters');
	} 
	
	public function filters ($filters=array()) {
		$filters = array_merge($filters, array(
			'acf/translate_field' => 'content',
			'acf/format_value' => 'content',
			'acf/input/admin_l10n' => 'content',
			'acf/taxonomy/admin_l10n' => 'content',
			'acf/post_type/admin_l10n' => 'content',
			'acf/fields/taxonomy/result' => 'content',
			'acf/fields/post_object/result' => 'content',
			'acf_the_content' => 'content',
			'acf/prepare_field' => [__CLASS__, 'label_attr'],
			'acf/acf_get_posts/results' => 'get_posts'
		));
		
		return $filters;
	}
	
	public static function label_attr ($field) {
		$field['label'] = Transliteration_Controller::get()->transliterate_no_html( $field['label'] );
		return $field;
	}
}