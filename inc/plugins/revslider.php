<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Plugin__revslider')) :
	class Serbian_Transliteration__Plugin__revslider extends Serbian_Transliteration
	{
		
		/* Run this script */
		public static function run($dry = false) {
			global $rstr_cache;
			$class = self::class;
			$instance = $rstr_cache->get($class);
			if ( !$instance ) {
				$instance = $rstr_cache->set($class, new self($dry));
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
				'revslider_add_static_layer_html' => array($classname, 'content'),
				'revslider_mod_stream_meta' => array($classname, 'content'),
				'revslider_add_layer_html' => array($classname, 'content')
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