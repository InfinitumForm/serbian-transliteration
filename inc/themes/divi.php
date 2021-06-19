<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Theme: Divi
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Theme__divi')) :
	class Serbian_Transliteration__Theme__divi extends Serbian_Transliteration
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
		
		function __construct($dry = array()){
			if($dry) return;
			$this->add_filter('rstr/transliteration/exclude/filters', array(get_class(), 'filters'));
		} 
		
		public static function filters ($filters=array()) {

			$classname = self::run(true);
			$filters = array_merge($filters, array(
				'et_before_main_content' => array($classname, 'content'),
				'et_after_main_content' => array($classname, 'content'),
				'et_before_content' => array($classname, 'content'),
				'et_html_top_header' => array($classname, 'content'),
				'et_html_slide_header' => array($classname, 'content'),
				'et_header_top' => array($classname, 'content'),
				'et_html_main_header' => array($classname, 'content')
			));
			
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