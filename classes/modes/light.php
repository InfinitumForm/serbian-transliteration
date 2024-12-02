<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Light extends Transliteration {
	use Transliteration__Cache;
    
	// Mode ID
	const MODE = 'light';
	
	/*
	 * The main constructor
	 */
    public function __construct() {

	}
	
	/*
	 * Get current instance
	 */
	public static function get() {
		return self::cached_static('instance', function(){
			return new self();
		});
	}
	
	/*
	 * Get available filters for this mode
	 */
	public function filters() {

		$filters = [
			'comment_text'			=> 'content',
			'comments_template' 	=> 'content',
		//	'the_content' 			=> 'content',
			'the_title' 			=> 'no_html_content',
			'the_date' 				=> 'no_html_content',
			'get_post_time' 		=> 'no_html_content',
			'get_the_date' 			=> 'no_html_content',
			'gettext' 				=> 'gettext_content',
			'ngettext' 				=> 'content',
			'gettext_with_context' 	=> 'content',
			'ngettext_with_context' => 'content',
			'wp_mail'				=> 'wp_mail',
			'wp_title' 				=> 'no_html_content',
			'pre_get_document_title'=> 'no_html_content',
			'default_post_metadata'	=> 'content',
			'option_blogdescription'=> 'no_html_content',
			'option_blogname' 		=> 'no_html_content',
			'get_the_terms'			=> 'transliteration_wp_terms', //Sydney, Blocksy, Colormag
			'wp_get_object_terms' 	=> 'transliteration_wp_terms', //Phlox
			'oceanwp_excerpt'		=> 'content', //Oceanwp
		//	'sanitize_title'		=> 'force_permalink_to_latin',
			'the_permalink'			=> 'force_permalink_to_latin',
			'wp_unique_post_slug'	=> 'force_permalink_to_latin',
			'document_title_parts' 	=> 'transliterate_objects',
			'the_post'				=> 'the_post_filter',
			'the_posts'				=> 'the_posts_filter'
		];

		if (!current_theme_supports( 'title-tag' )){
			unset($filters['pre_get_document_title']);
		} else {
			unset($filters['wp_title']);
		}

		return $filters;
	}
    
}