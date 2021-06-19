<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Plugin: WooCommerce
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Plugin__woocommerce')) :
	class Serbian_Transliteration__Plugin__woocommerce extends Serbian_Transliteration
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

		function __construct($dry = false){
			if($dry) return;
			$this->add_filter('rstr/transliteration/exclude/filters', array(get_class(), 'filters'));
		}

		public static function filters ($filters=array()) {

			$classname = self::run(true);
			$filters = array_merge($filters, array(
				'woocommerce_product_single_add_to_cart_text' => array($classname, 'content'),
					'woocommerce_email_footer_text' => array($classname, 'content'),
					'woocommerce_get_availability_text' => array($classname, 'no_html_content'),
					'woocommerce_get_price_html_from_text' => array($classname, 'content'),
					'woocommerce_order_button_text' => array($classname, 'no_html_content'),
					'woocommerce_pay_order_button_text' => array($classname, 'content'),
					'filter_woocommerce_product_add_to_cart_text' => array($classname, 'content'),
					'woocommerce_product_single_add_to_cart_text' => array($classname, 'content'),
					'woocommerce_thankyou_order_received_text' => array($classname, 'content'),
					'wc_add_to_cart_message_html' => array($classname, 'content'),
					'woocommerce_admin_stock_html' => array($classname, 'content'),
					'woocommerce_cart_no_shipping_available_html' => array($classname, 'content'),
					'sale_price_dates_from' => array($classname, 'content'),
					'sale_price_dates_to' => array($classname, 'content'),
					'woocommerce_dropdown_variation_attribute_options_html' => array($classname, 'content'),
					'woocommerce_date_input_html_pattern' => array($classname, 'content'),
					'woocommerce_cart_totals_taxes_total_html' => array($classname, 'content'),
					'woocommerce_cart_totals_fee_html' => array($classname, 'content'),
					'woocommerce_cart_totals_coupon_html' => array($classname, 'content'),
					'woocommerce_cart_totals_order_total_html' => array($classname, 'content'),
					'woocommerce_coupon_discount_amount_html' => array($classname, 'content'),
					'woocommerce_empty_price_html' => array($classname, 'content'),
					'woocommerce_grouped_price_html' => array($classname, 'content'),
					'woocommerce_grouped_empty_price_html' => array($classname, 'content'),
					'woocommerce_get_stock_html' => array($classname, 'content'),
					'woocommerce_get_price_html_from_to' => array($classname, 'content'),
					'woocommerce_get_price_html' => array($classname, 'content'),
					'woocommerce_layered_nav_term_html' => array($classname, 'content'),
					'woocommerce_no_shipping_available_html' => array($classname, 'content'),
					'woocommerce_order_item_quantity_html' => array($classname, 'content'),
					'woocommerce_order_button_html' => array($classname, 'content'),
					'woocommerce_product_get_rating_html' => array($classname, 'content'),
					'woocommerce_pay_order_button_html' => array($classname, 'content'),
					'wc_payment_gateway_form_saved_payment_methods_html' => array($classname, 'content'),
					'woocommerce_subcategory_count_html' => array($classname, 'content'),
					'woocommerce_get_stock_html' => array($classname, 'content'),
					'woocommerce_single_product_image_thumbnail_html' => array($classname, 'content'),
					'woocommerce_variable_price_html' => array($classname, 'content'),
					'woocommerce_variable_empty_price_html' => array($classname, 'content')
			));
			asort($filters);
			return $filters;
		}

		public function content ($content='') {
			if(empty($content)) return $content;


			if(is_array($content))
			{
				if(method_exists($this, 'transliterate_objects')) {
					$content = $this->transliterate_objects($content);
				}
			}
			else if(is_string($content))
			{

				if(method_exists($this, 'transliterate_text')) {
					$content = $this->transliterate_text($content);
				}
			}
			return $content;
		}

		public function no_html_content ($content='') {
			if(empty($content)) return $content;

			if(is_array($content))
			{
				if(method_exists($this, 'transliterate_objects')) {
					$content = $this->transliterate_objects($content, NULL, false);
				}
			}
			else if(is_string($content))
			{

				if(method_exists($this, 'transliterate_text')) {
					$content = $this->transliterate_text($content, NULL, false);
				}
			}
			return $content;
		}
	}
endif;
