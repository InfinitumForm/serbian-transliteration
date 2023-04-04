<?php if ( ! defined( 'WPINC' ) )	die( "Don't mess with us." );
/**
 * Dev Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if ( ! class_exists( 'Serbian_Transliteration_Mode_Dev' ) ) :
	class Serbian_Transliteration_Mode_Dev extends Serbian_Transliteration {

		/* Run this script */
		public static function run() {
			$class = self::class;
			$instance = Serbian_Transliteration_Cache::get($class);
			if ( !$instance ) {
				$instance = Serbian_Transliteration_Cache::set($class, new self());
			}
			return $instance;
		}

		public static function filters( $options = array() ) {
			global $pagenow;

			if ( empty( $options ) ) {
				$options = get_rstr_option();
			}

			$filters = array(
				'comment_text'			=> 'content',
				'comments_template' 	=> 'content',
				'the_content' 			=> 'content',
				'the_content_more_link' => 'content',
				'wp_nav_menu_items' 	=> 'content',
				'default_post_metadata'	=> 'content',
				'get_comment_metadata' 	=> 'content',
				'get_term_metadata' 	=> 'content',
				'get_user_metadata' 	=> 'content',
				'get_post_metadata' 	=> 'content',
				'get_page_metadata' 	=> 'content',
				'gettext' 				=> 'content',
				'ngettext' 				=> 'content',
				'gettext_with_context' 	=> 'content',
				'ngettext_with_context' => 'content'
			);
			
			asort($filters);

			if (!current_theme_supports( 'title-tag' )){
				unset($filters['document_title_parts']);
				unset($filters['pre_get_document_title']);
			} else {
				unset($filters['wp_title']);
			}

			return $filters;
		}

		public function __construct() {
			$filters = self::filters($this->get_options());
			$filters = apply_filters('rstr/transliteration/exclude/dev', $filters, $this->get_options());
			
			if ( !is_admin() ) {
				foreach($filters as $key=>$function){
				//	$this->add_filter($key, $function, (PHP_INT_MAX-1), 1);
				}
			}
		}
		
		public static function execute_buffer() {
			add_action('wp_loaded', array(__CLASS__, 'output_buffer_start'), (PHP_INT_MAX-10));
			add_action('shutdown', array(__CLASS__, 'output_buffer_end'), (PHP_INT_MAX-10));
		}
		
		public static function output_buffer_start() {
			ob_start(array(__CLASS__, 'output_callback'), 0, PHP_OUTPUT_HANDLER_REMOVABLE);
		}

		public static function output_buffer_end() {
			ob_get_clean();
		}

		public static function output_callback ($buffer='') {

			if(!(defined('DOING_AJAX') && DOING_AJAX))
			{
				$p = new WP_HTML_Tag_Processor( $buffer );
				
				while($p->next_tag('div')) {
					$p->set_attribute( 'data-transliterated', 'true' );
				}
				
				$buffer = $p->get_updated_html();
			}

			return $buffer;
		}

		public function content( $content = '' ) {
			if ( empty( $content ) ) {
				return $content;
			}

			if ( is_array( $content ) ) {
				$content = $this->title_parts( $content );
			} else if ( is_string( $content ) && ! is_numeric( $content ) ) {
				$content = $this->cyr_to_lat( $content );
			}

			return $content;
		}
	}
endif;