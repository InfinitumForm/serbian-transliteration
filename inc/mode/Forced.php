<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Forced Transliteration Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if(!class_exists('Serbian_Transliteration_Mode_Forced')) :
class Serbian_Transliteration_Mode_Forced extends Serbian_Transliteration
{

	/* Run this script */
	public static function run() {
		$class = self::class;
		$instance = Serbian_Transliteration_Cache::get($class);
		if ( !$instance ) {
			$instance = Serbian_Transliteration_Cache::set($class, new self());
		}
		return $instance;
	}

	public static function filters ($options=array()) {
		if(empty($options)) $options = get_rstr_option();

		$filters = array(
			'single_cat_title'				=> 'no_html_content',
			'the_category'					=> 'content',
			'wp_list_categories'			=> 'content',//Widget categories
			'wp_dropdown_cats'				=> 'content',//Widget categories dropdown
			'get_archives_link'				=> 'content', //Widget achives
			'get_the_terms'					=> 'transliteration_wp_terms',//Sydney, Blocksy, Colormag
			'get_the_excerpt' 				=> 'content',
			'the_excerpt'					=> 'content',
			'oceanwp_excerpt'				=> 'content',//Oceanwp
			'get_calendar' 					=> 'content',
		//	'pre_kses' 						=> 'content',
			'date_i18n'						=> 'content',
			'get_comment_date' 				=> 'no_html_content',
			'wp_get_object_terms' 			=> 'transliteration_wp_terms', //Phlox
			'comment_text'					=> 'content',
			'comments_template' 			=> 'content',
			'the_content' 					=> 'content',
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
			'sanitize_title' 				=> 'no_html_content',
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
			'wp_get_attachment_image_attributes' => 'image_attributes'
		);
		asort($filters);

		return $filters;
	}
	
	public function objects ($obj) {
		return $this->transliterate_objects($obj);
	}

	public function __construct(){
		$filters = self::filters($this->get_options());
		$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->get_options());
		$filters = apply_filters('rstr/transliteration/exclude/filters/forced', $filters, $this->get_options());

		$mode = new Serbian_Transliteration_Mode();

		if(!is_admin())
		{				
			$args = 1;
			foreach($filters as $key=>$method){
				
				if( 'gettext' == $key ) {
					$args = 3;
				}
				
				do_action('rstr/transliteration/filter/arguments/forced/before', $key, $method);
				
				if( is_string($method) ) {
					if( method_exists($mode, $method) ) {
						$this->add_filter($key, [$mode, $method], (PHP_INT_MAX-1), $args);
					} else if( method_exists($this, $method) ) {
						$this->add_filter($key, [$this, $method], (PHP_INT_MAX-1), $args);
					}
				}
				
				if( 'gettext' == $key ) {
					$args = 1;
				}
				
				do_action('rstr/transliteration/filter/arguments/forced/after', $key, $method);
			}
		}

		$this->add_filter('bloginfo', [$mode, 'bloginfo'], (PHP_INT_MAX-1), 2);
		$this->add_filter('bloginfo_url', [$mode, 'bloginfo'], (PHP_INT_MAX-1), 2);
	}
	
	public static function execute_buffer() {
		if(!is_admin())
		{
			if(get_rstr_option('enable-rss', 'no') == 'yes')
			{
				add_action('rss_head', array(__CLASS__, 'rss_output_buffer_start'), (PHP_INT_MAX-1));
				add_action('rss_footer', array(__CLASS__, 'rss_output_buffer_end'), (PHP_INT_MAX-1));

				add_action('rss2_head', array(__CLASS__, 'rss_output_buffer_start'), (PHP_INT_MAX-1));
				add_action('rss2_footer', array(__CLASS__, 'rss_output_buffer_end'), (PHP_INT_MAX-1));

				add_action('rdf_head', array(__CLASS__, 'rss_output_buffer_start'), (PHP_INT_MAX-1));
				add_action('rdf_footer', array(__CLASS__, 'rss_output_buffer_end'), (PHP_INT_MAX-1));

				add_action('atom_head', array(__CLASS__, 'rss_output_buffer_start'), (PHP_INT_MAX-1));
				add_action('atom_footer', array(__CLASS__, 'rss_output_buffer_end'), (PHP_INT_MAX-1));
			}

			if(get_rstr_option('force-widgets', 'no') == 'yes')
			{
				add_action('dynamic_sidebar_before', array(__CLASS__, 'rss_output_buffer_start'), (PHP_INT_MAX-1));
				add_action('dynamic_sidebar_after', array(__CLASS__, 'rss_output_buffer_end'), (PHP_INT_MAX-1));
			}

			add_action('wp_loaded', array(__CLASS__, 'output_buffer_start'), (PHP_INT_MAX-1));
			add_action('shutdown', array(__CLASS__, 'output_buffer_end'), (PHP_INT_MAX-1));
		}
	}

	public static function output_buffer_start() {
		ob_start(NULL, 0, PHP_OUTPUT_HANDLER_REMOVABLE);
	}

	public static function output_buffer_end() {
		$buffer = '';
		if (ob_get_level()) {
			$buffer = ob_get_contents();
			ob_end_clean();
		}

		if(empty($buffer)) return $buffer;

		if(!(defined('DOING_AJAX') && DOING_AJAX))
		{
			$sufix = '_' . strlen($buffer);

			$forced_cache = Serbian_Transliteration_DB_Cache::get( $this->transient.$sufix );

			if (!is_admin() && empty($forced_cache) )
			{
				$buffer = preg_replace_callback('/(?=<div(.*?)>)(.*?)(?<=<\/div>)/s', function($matches) {
					$matches[2] = $this->transliterate_text($matches[2]);
					return $matches[2];
				}, $buffer);

				if(!empty($buffer)) {
					Serbian_Transliteration_DB_Cache::set( $this->transient.$sufix, $buffer, MINUTE_IN_SECONDS*3 );
				}
			}
			else
			{
				$buffer = $forced_cache;
			}
		}

		echo $buffer;
	}

	public static function rss_output_buffer_start() {
		ob_start(NULL, 0, PHP_OUTPUT_HANDLER_REMOVABLE);
	}

	public static function rss_output_buffer_end() {
		$output = '';
		if (ob_get_level()) {
			$output = ob_get_contents();
			ob_end_clean();
		}

		$output = self::run()->transliterate_text($output);

		echo $output;
	}
}
endif;