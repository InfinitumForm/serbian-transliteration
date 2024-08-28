<?php if ( !defined('WPINC') ) die();
/**
 * Active Theme: Themify
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Theme_Themify extends Transliteration
{
	function __construct(){
		$this->add_filter('transliteration_mode_filters', 'filters');
	} 
	
	public function filters ($filters=array()) {
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