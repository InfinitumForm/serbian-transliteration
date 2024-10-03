<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Advanced {
    
	// Mode ID
	const MODE = 'advanced';
	
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
			'single_cat_title'				=> 'no_html_content',
			'the_category'					=> 'content',
			'wp_list_categories'			=> 'content',//Widget categories
			'wp_dropdown_cats'				=> 'content',//Widget categories dropdown
			'get_archives_link'				=> 'content', //Widget achives
			'get_the_terms'					=> 'transliteration_wp_terms',//Sydney, Blocksy, Colormag
			'get_the_excerpt' 				=> 'content',
		//	'the_excerpt'					=> 'content',
			'oceanwp_excerpt'				=> 'content',//Oceanwp
			'get_calendar' 					=> 'content',
		//	'pre_kses' 						=> 'content',
			'date_i18n'						=> 'content',
			'get_comment_date' 				=> 'no_html_content',
			'wp_get_object_terms' 			=> 'transliteration_wp_terms', //Phlox
			'comment_text'					=> 'content',
			'comments_template' 			=> 'content',
		//	'the_content' 					=> 'content',
			'the_title' 					=> 'no_html_content',
			'wp_nav_menu_items' 			=> 'content',
			'get_post_time' 				=> 'content',
			'wp_title' 						=> 'no_html_content',
			'the_date' 						=> 'no_html_content',
			'get_the_date' 					=> 'no_html_content',
			'the_content_more_link' 		=> 'content',
			'pre_get_document_title'		=> 'no_html_content',
			'default_post_metadata' 		=> 'content',
			'get_comment_metadata' 			=> 'content',
			'get_term_metadata' 			=> 'content',
			'get_user_metadata' 			=> 'content',
			'get_post_metadata' 			=> 'content',
			'get_page_metadata' 			=> 'content',
			'gettext' 						=> 'gettext_content',
			'ngettext' 						=> 'content',
			'gettext_with_context' 			=> 'content',
			'ngettext_with_context' 		=> 'content',
			'widget_text' 					=> 'content',
			'widget_title' 					=> 'no_html_content',
			'widget_text_content' 			=> 'content',
			'widget_custom_html_content' 	=> 'content',
		//	'sanitize_title' 				=> 'no_html_content',
			'wp_unique_post_slug' 			=> 'no_html_content',
			'option_blogdescription'		=> 'no_html_content',
			'option_blogname' 				=> 'no_html_content',
			'document_title_parts' 			=> 'transliterate_objects',
			'sanitize_title'				=> 'force_permalink_to_latin',
			'the_permalink'					=> 'force_permalink_to_latin',
			'wp_unique_post_slug'			=> 'force_permalink_to_latin',
			'wp_mail'						=> 'wp_mail',
			'register_post_type_args'		=> 'objects',
			'render_block'					=> 'content',
			'wp_get_attachment_image_attributes' => 'image_attributes',
			'the_post'						=> 'the_post_filter',
			'the_posts'						=> 'the_posts_filter'
		);

		if (!current_theme_supports( 'title-tag' )){
			unset($filters['pre_get_document_title']);
		} else {
			unset($filters['wp_title']);
		}

		return $filters;
	}
    
}