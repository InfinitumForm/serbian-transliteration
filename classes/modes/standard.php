<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Standard {
    
	// Mode ID
	const MODE = 'standard';
	
	/*
	 * The main constructor
	 */
    public function __construct() {
		
    }
	
	/*
	 * Get current instance
	 */
	private static $instance = NULL;
	public static function get() {
		if( NULL === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/*
	 * Get available filters for this mode
	 */
	public function filters() {
		$filters = array(
			'comment_text'			=> 'content',
			'comments_template' 	=> 'content',
		//	'the_content' 			=> 'content',
			'the_title' 			=> 'no_html_content',
			'the_date' 				=> 'no_html_content',
			'get_post_time' 		=> 'no_html_content',
			'get_the_date' 			=> 'no_html_content',
			'the_content_more_link' => 'content',
			'wp_nav_menu_items' 	=> 'content',
			'wp_title' 				=> 'no_html_content',
			'pre_get_document_title'=> 'no_html_content',
			'default_post_metadata'	=> 'content',
			'get_comment_metadata' 	=> 'content',
			'get_term_metadata' 	=> 'content',
			'get_user_metadata' 	=> 'content',
			'get_post_metadata' 	=> 'content',
			'get_page_metadata' 	=> 'content',
			'gettext' 				=> 'gettext_content',
			'ngettext' 				=> 'content',
			'gettext_with_context' 	=> 'content',
			'ngettext_with_context' => 'content',
			'option_blogdescription'=> 'no_html_content',
			'option_blogname' 		=> 'no_html_content',
			'document_title_parts' 	=> 'transliterate_objects',
			'get_the_terms'			=> 'transliteration_wp_terms',//Sydney, Blocksy, Colormag
			'wp_get_object_terms' 	=> 'transliteration_wp_terms', //Phlox
		//	'sanitize_title'		=> 'force_permalink_to_latin',
			'the_permalink'			=> 'force_permalink_to_latin',
			'wp_unique_post_slug'	=> 'force_permalink_to_latin',
			'wp_mail'				=> 'wp_mail',
			'render_block'			=> 'content',
			'wp_get_attachment_image_attributes' => 'image_attributes',
			'the_post'				=> 'the_post_filter',
			//Oceanwp
			'oceanwp_excerpt'		=> 'content',
			'the_posts'				=> 'the_posts_filter'
		);

		if (!current_theme_supports( 'title-tag' )){
			unset($filters['pre_get_document_title']);
		} else {
			unset($filters['wp_title']);
		}

		return $filters;
	}
    
}