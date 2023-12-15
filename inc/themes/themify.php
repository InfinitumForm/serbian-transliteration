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
				'tf_hook_body_start' => 'content',
				'tf_hook_header_before' => 'content',
				'tf_hook_header_start' => 'content',
				'tf_hook_header_end' => 'content',
				'tf_hook_header_after' => 'content',
				'tf_hook_layout_before' => 'content',
				'tf_hook_content_before' => 'content',
				'tf_hook_content_start' => 'content',
				'tf_hook_post_before' => 'content',
				'tf_hook_post_start' => 'content',
				'tf_hook_post_end' => 'content',
				'tf_hook_post_after' => 'content',
				'tf_hook_comment_before' => 'content',
				'tf_hook_comment_start' => 'content',
				'tf_hook_commentform_before' => 'content',
				'tf_hook_commentform_start' => 'content',
				'tf_hook_commentform_end' => 'content',
				'tf_hook_commentform_after' => 'content',
				'tf_hook_comment_end' => 'content',
				'tf_hook_comment_after' => 'content',
				'tf_hook_content_end' => 'content',
				'tf_hook_content_after' => 'content',
				'tf_hook_sidebar_before' => 'content',
				'tf_hook_sidebar_start' => 'content',
				'tf_hook_sidebar_end' => 'content',
				'tf_hook_sidebar_after' => 'content',
				'tf_hook_layout_after' => 'content',
				'tf_hook_footer_before' => 'content',
				'tf_hook_footer_start' => 'content',
				'tf_hook_footer_end' => 'content',
				'tf_hook_footer_after' => 'content',
				'tf_hook_body_end' => 'content'
			));
			
			return $filters;
		}
	}
endif;