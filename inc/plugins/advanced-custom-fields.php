<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
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
				/*'acf/translate_field' => array($classname, 'content'),
				'acf/render_fields' => array($classname, 'content'),
				'acf/load_fields' => array($classname, 'content'),
				'acf/translate_field_group' => array($classname, 'content'),
				'acf/load_meta' => array($classname, 'content'),
				'acf/load_reference' => array($classname, 'content'),
				'acf/load_value' => array($classname, 'content'),
				'acf/format_value' => array($classname, 'content'),
				'acf/field_group/admin_l10n' => array($classname, 'content'),
				'acf/input/admin_l10n' => array($classname, 'content'),
				'acf_the_editor_content' => array($classname, 'content')*/
			));
			
			return $filters;
		}
		
		public function content ($content='') { echo '<pre>', var_dump($content), '</pre>';
			if(empty($content) && !is_string($content)) return $content;
			
			
			if(is_array($content))
			{
				if(method_exists($this, 'transliterate_objects')) {
					$content = $this->transliterate_objects($content);
				}
			}
			else if(is_string($content))
			{
					
				if(method_exists($this, 'transliterate_text')) {
					$content = $this->transliterate_text($content);
				}
			}
			return $content;
		}
	}
endif;