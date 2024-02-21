<?php if ( !defined('WPINC') ) die();
/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Plugin__advanced_custom_fields')) :
	class Serbian_Transliteration__Plugin__advanced_custom_fields extends Serbian_Transliteration
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
				'acf/translate_field' => 'content',
				'acf/format_value' => 'content',
				'acf/input/admin_l10n' => 'content',
				'acf/taxonomy/admin_l10n' => 'content',
				'acf/post_type/admin_l10n' => 'content',
				'acf/fields/taxonomy/result' => 'content',
				'acf/fields/post_object/result' => 'content',
				'acf_the_content' => 'content',
				'acf/prepare_field' => 'label_attr',
				'acf/acf_get_posts/results' => 'get_posts'
			));
			
			return $filters;
		}
	}
endif;