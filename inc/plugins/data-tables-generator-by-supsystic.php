<?php if ( !defined('WPINC') ) die();
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
				"{$pfx}_after_contactform_loaded" => 'content',
				// "{$pfx}_after_core_loaded" => 'content',
				// "{$pfx}_after_diagram_loaded" => 'content',
				// "{$pfx}_after_migrationfree_loaded" => 'content',
				// "{$pfx}_after_overview_loaded" => 'content',
				// "{$pfx}_after_promo_loaded" => 'content',
				// "{$pfx}_after_settings_loaded" => 'content',
				"{$pfx}_after_tables_loaded" => 'content',
				// "{$pfx}_after_exporter_loaded" => 'content',
				// "{$pfx}_after_importer_loaded" => 'content',
				// "{$pfx}_after_migration_loaded" => 'content',
				"{$pfx}_after_modules_loaded" => 'content',
				"{$pfx}_tables_get" => 'content',
				"{$pfx}_before_table_render" => 'content',
				"{$pfx}_before_table_render_from_cache" => 'content',
			));
			asort($filters);
			return $filters;
		}
	}
endif;