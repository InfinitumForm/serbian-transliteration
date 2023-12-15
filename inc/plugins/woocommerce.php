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
				'woocommerce_product_single_add_to_cart_text' => 'content',
					'woocommerce_email_footer_text' => 'content',
					'woocommerce_get_availability_text' => 'no_html_content',
					'woocommerce_get_price_html_from_text' => 'content',
					'woocommerce_order_button_text' => 'no_html_content',
					'woocommerce_pay_order_button_text' => 'content',
					'filter_woocommerce_product_add_to_cart_text' => 'content',
					'woocommerce_product_single_add_to_cart_text' => 'content',
					'woocommerce_thankyou_order_received_text' => 'content',
					'wc_add_to_cart_message_html' => 'content',
					'woocommerce_admin_stock_html' => 'content',
					'woocommerce_cart_no_shipping_available_html' => 'content',
					'sale_price_dates_from' => 'content',
					'sale_price_dates_to' => 'content',
					'woocommerce_dropdown_variation_attribute_options_html' => 'content',
					'woocommerce_date_input_html_pattern' => 'content',
					'woocommerce_cart_totals_taxes_total_html' => 'content',
					'woocommerce_cart_totals_fee_html' => 'content',
					'woocommerce_cart_totals_coupon_html' => 'content',
					'woocommerce_cart_totals_order_total_html' => 'content',
					'woocommerce_coupon_discount_amount_html' => 'content',
					'woocommerce_empty_price_html' => 'content',
					'woocommerce_grouped_price_html' => 'content',
					'woocommerce_grouped_empty_price_html' => 'content',
					'woocommerce_get_stock_html' => 'content',
					'woocommerce_get_price_html_from_to' => 'content',
					'woocommerce_get_price_html' => 'content',
					'woocommerce_layered_nav_term_html' => 'content',
					'woocommerce_no_shipping_available_html' => 'content',
					'woocommerce_order_item_quantity_html' => 'content',
					'woocommerce_order_button_html' => 'content',
					'woocommerce_product_get_rating_html' => 'content',
					'woocommerce_pay_order_button_html' => 'content',
					'wc_payment_gateway_form_saved_payment_methods_html' => 'content',
					'woocommerce_subcategory_count_html' => 'content',
					'woocommerce_get_stock_html' => 'content',
					'woocommerce_single_product_image_thumbnail_html' => 'content',
					'woocommerce_variable_price_html' => 'content',
					'woocommerce_variable_empty_price_html' => 'content',
					'woocommerce_currency_symbol' => 'content',
					'woocommerce_currencies' => 'content',
					'woocommerce_countries' => 'content',
					'woocommerce_countries_shipping_countries' => 'content',
					'woocommerce_countries_allowed_countries' => 'content',
					'woocommerce_get_country_name' => 'content',
					'woocommerce_country_locale' => 'content',
					'woocommerce_get_shipping_countries' => 'content',
					'woocommerce_get_allowed_countries' => 'content'
			));
			asort($filters);			
			return $filters;
		}
	}
endif;
