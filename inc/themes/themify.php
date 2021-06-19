<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Theme: Themify
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Theme__themify')) :
	class Serbian_Transliteration__Theme__themify extends Serbian_Transliteration
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
				'tf_hook_body_start' => array($classname, 'content'),
				'tf_hook_header_before' => array($classname, 'content'),
				'tf_hook_header_start' => array($classname, 'content'),
				'tf_hook_header_end' => array($classname, 'content'),
				'tf_hook_header_after' => array($classname, 'content'),
				'tf_hook_layout_before' => array($classname, 'content'),
				'tf_hook_content_before' => array($classname, 'content'),
				'tf_hook_content_start' => array($classname, 'content'),
				'tf_hook_post_before' => array($classname, 'content'),
				'tf_hook_post_start' => array($classname, 'content'),
				'tf_hook_post_end' => array($classname, 'content'),
				'tf_hook_post_after' => array($classname, 'content'),
				'tf_hook_comment_before' => array($classname, 'content'),
				'tf_hook_comment_start' => array($classname, 'content'),
				'tf_hook_commentform_before' => array($classname, 'content'),
				'tf_hook_commentform_start' => array($classname, 'content'),
				'tf_hook_commentform_end' => array($classname, 'content'),
				'tf_hook_commentform_after' => array($classname, 'content'),
				'tf_hook_comment_end' => array($classname, 'content'),
				'tf_hook_comment_after' => array($classname, 'content'),
				'tf_hook_content_end' => array($classname, 'content'),
				'tf_hook_content_after' => array($classname, 'content'),
				'tf_hook_sidebar_before' => array($classname, 'content'),
				'tf_hook_sidebar_start' => array($classname, 'content'),
				'tf_hook_sidebar_end' => array($classname, 'content'),
				'tf_hook_sidebar_after' => array($classname, 'content'),
				'tf_hook_layout_after' => array($classname, 'content'),
				'tf_hook_footer_before' => array($classname, 'content'),
				'tf_hook_footer_start' => array($classname, 'content'),
				'tf_hook_footer_end' => array($classname, 'content'),
				'tf_hook_footer_after' => array($classname, 'content'),
				'tf_hook_body_end' => array($classname, 'content')
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