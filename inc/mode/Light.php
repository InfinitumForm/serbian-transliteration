<?php if ( ! defined( 'WPINC' ) )	die( "Don't mess with us." );
/**
 * Light Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if ( ! class_exists( 'Serbian_Transliteration_Mode_Light' ) ) :
	class Serbian_Transliteration_Mode_Light extends Serbian_Transliteration {

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
			
			asort($filters);

			if (!current_theme_supports( 'title-tag' )){
				unset($filters['pre_get_document_title']);
			} else {
				unset($filters['wp_title']);
			}

			return $filters;
		}

		public function __construct() {
			$filters = self::filters($this->get_options());
			$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->get_options());
			$filters = apply_filters('rstr/transliteration/exclude/filters/light', $filters, $this->get_options());

			$mode = new Serbian_Transliteration_Mode();
			$args = 1;

			if (!is_admin()) {
				foreach ($filters as $key => $method) {
					$args = $key === 'gettext' ? 3 : 1;
					
					do_action('rstr/transliteration/filter/arguments/light/before', $key, $method);

					if( is_array($method) ) {
						$this->add_filter($key, $method, (PHP_INT_MAX - 1), $args);
					} else if (is_string($method)) {
						$target = method_exists($mode, $method) ? $mode : (method_exists($this, $method) ? $this : null);
						if ($target) {
							$this->add_filter($key, [$target, $method], (PHP_INT_MAX - 1), $args);
						}
					}

					do_action('rstr/transliteration/filter/arguments/light/after', $key, $method);
				}
				
				
				$this->add_action('wp_head', [&$this, 'buffer_start'], 1);
				$this->add_action('wp_print_footer_scripts', [&$this, 'buffer_end'], PHP_INT_MAX-10);
				
				$this->add_action('woocommerce_before_main_content', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_main_content', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_template_part', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_template_part', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_mini_cart', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_mini_cart', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_cart_totals', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_cart_totals', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_cart', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_cart', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_shipping_calculator', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_shipping_calculator', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_checkout_form', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_checkout_form', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_thankyou', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_thankyou', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_before_cart', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_after_cart', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_review_order_before_cart_contents', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_review_order_after_cart_contents', 'buffer_end', PHP_INT_MAX-10, 0);
				
				$this->add_action('woocommerce_order_details_before_order_table', 'buffer_start', 1, 0);
				$this->add_action('woocommerce_order_details_after_order_table', 'buffer_end', PHP_INT_MAX-10, 0);
				
			}
			
			
		}
		
		function buffer_start() { ob_start([&$this, 'callback_function'], 0, PHP_OUTPUT_HANDLER_REMOVABLE); }
		function buffer_end() {
			if (ob_get_level()) {
				ob_end_flush();
			}
		}

		function callback_function($buffer) {
			$buffer = $this->transliterate_text($buffer);
			return $buffer;
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