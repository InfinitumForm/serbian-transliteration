<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Standard Transliteration Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if(!class_exists('Serbian_Transliteration_Mode_Standard')) :
class Serbian_Transliteration_Mode_Standard extends Serbian_Transliteration
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
			'comment_text'			=> 'content',
			'comments_template' 	=> 'content',
			'the_content' 			=> 'content',
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
			'sanitize_title'		=> 'force_permalink_to_latin',
			'the_permalink'			=> 'force_permalink_to_latin',
			'wp_unique_post_slug'	=> 'force_permalink_to_latin',
			'wp_mail'				=> 'wp_mail',
			'render_block'			=> 'content',
			'wp_get_attachment_image_attributes' => 'image_attributes'
		);

		if (!current_theme_supports( 'title-tag' )){
			unset($filters['document_title_parts'], $filters['pre_get_document_title']);
		} else {
			unset($filters['wp_title']);
		}
		
		asort($filters);

		return $filters;
	}

	public function __construct() {
		$filters = self::filters($this->get_options());
		$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->get_options());
		$filters = apply_filters('rstr/transliteration/exclude/filters/standard', $filters, $this->get_options());

		$mode = new Serbian_Transliteration_Mode();
		$args = 1;

		if (!is_admin()) {
			foreach ($filters as $key => $method) {
				$args = $key === 'gettext' ? 3 : 1;
				
				do_action('rstr/transliteration/filter/arguments/standard/before', $key, $method);

				if( is_array($method) ) {
					$this->add_filter($key, $method, (PHP_INT_MAX - 1), $args);
				} else if (is_string($method)) {
					$target = method_exists($mode, $method) ? $mode : (method_exists($this, $method) ? $this : null);
					if ($target) {
						$this->add_filter($key, [$target, $method], (PHP_INT_MAX - 1), $args);
					}
				}

				do_action('rstr/transliteration/filter/arguments/standard/after', $key, $method);
			}
		}

		$this->add_filter('bloginfo', [$mode, 'bloginfo'], (PHP_INT_MAX - 1), 2);
		$this->add_filter('bloginfo_url', [$mode, 'bloginfo'], (PHP_INT_MAX - 1), 2);
	}

	
	public static function execute_buffer() {
		if (!is_admin()) {
			$priority = PHP_INT_MAX - 1;
			$actions = [
				'rss_head', 'rss_footer',
				'rss2_head', 'rss2_footer',
				'rdf_head', 'rdf_footer',
				'atom_head', 'atom_footer',
			];

			if (get_rstr_option('enable-rss', 'no') === 'yes') {
				foreach ($actions as $action) {
					add_action($action, [__CLASS__, 'rss_output_buffer_' . (strpos($action, '_head') ? 'start' : 'end')], $priority);
				}
			}

			if (get_rstr_option('force-widgets', 'no') === 'yes') {
				add_action('dynamic_sidebar_before', [__CLASS__, 'rss_output_buffer_start'], $priority);
				add_action('dynamic_sidebar_after', [__CLASS__, 'rss_output_buffer_end'], $priority);
			}
		}
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

		$output = self::get()->transliterate_text($output);

		echo $output;
	}
}
endif;
