<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Mode_Light', false) ) : class Transliteration_Mode_Light extends Transliteration {
    
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
		$filters = [
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
			'the_title' 			=> 'no_html_content',
			'get_the_terms'			=> 'transliteration_wp_terms', //Sydney, Blocksy, Colormag
			'wp_get_object_terms' 	=> 'transliteration_wp_terms', //Phlox
			'oceanwp_excerpt'		=> 'content', //Oceanwp
			'sanitize_title'		=> 'force_permalink_to_latin',
			'the_permalink'			=> 'force_permalink_to_latin',
			'wp_unique_post_slug'	=> 'force_permalink_to_latin',
			'document_title_parts' 	=> 'transliterate_objects'
		];

		if (!current_theme_supports( 'title-tag' )){
			unset($filters['pre_get_document_title']);
		} else {
			unset($filters['wp_title']);
		}

		return $filters;
	}
    
} endif;