<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Plugin: Data tables generator by supsystic
 *
 * @link              http://infinitumform.com/
 * @since             1.3.5
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Plugin__data_tables_generator_by_supsystic')) :
	class Serbian_Transliteration__Plugin__data_tables_generator_by_supsystic extends Serbian_Transliteration
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
			$pfx = 'supsystic_tbl';
			$filters = array_merge($filters, array(
				"{$pfx}_after_contactform_loaded" => array($classname, 'content'),
				// "{$pfx}_after_core_loaded" => array($classname, 'content'),
				// "{$pfx}_after_diagram_loaded" => array($classname, 'content'),
				// "{$pfx}_after_migrationfree_loaded" => array($classname, 'content'),
				// "{$pfx}_after_overview_loaded" => array($classname, 'content'),
				// "{$pfx}_after_promo_loaded" => array($classname, 'content'),
				// "{$pfx}_after_settings_loaded" => array($classname, 'content'),
				"{$pfx}_after_tables_loaded" => array($classname, 'content'),
				// "{$pfx}_after_exporter_loaded" => array($classname, 'content'),
				// "{$pfx}_after_importer_loaded" => array($classname, 'content'),
				// "{$pfx}_after_migration_loaded" => array($classname, 'content'),
				"{$pfx}_after_modules_loaded" => array($classname, 'content'),
				"{$pfx}_tables_get" => array($classname, 'content'),
				"{$pfx}_before_table_render" => array($classname, 'content'),
				"{$pfx}_before_table_render_from_cache" => array($classname, 'content'),
			));
			asort($filters);
			return $filters;
		}
		
		public function content ($content='') {
			if(empty($content)) return $content;
			
			
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