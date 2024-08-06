<?php if ( !defined('WPINC') ) die();
/**
 * Active Plugin: WooCommerce
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Transliteration_Plugin_Woocommerce', false)) :
	class Transliteration_Plugin_Woocommerce extends Transliteration
	{
		function __construct(){
			$this->add_filter('transliteration_mode_filters', 'filters');
		} 
		
		public function filters ($filters=array()) {
			$filters = array_merge($filters, array(
				'woocommerce_shipping_not_enabled_on_cart_html' => 'content',
				'woocommerce_shipping_may_be_available_html' => 'content',
				'woocommerce_cart_item_remove_link' => 'content',
				'woocommerce_cart_item_backorder_notification' => 'content',
				'woocommerce_product_cross_sells_products_heading' => 'content',
				'woocommerce_widget_cart_item_quantity' => 'content',
				'woocommerce_checkout_must_be_logged_in_message' => 'content',
				'woocommerce_checkout_coupon_message' => 'content',
				'woocommerce_checkout_login_message' => 'content',
				'woocommerce_no_available_payment_methods_message' => 'content',
				
				'woocommerce_order_item_name' => 'content',
				'woocommerce_sale_flash' => 'content',
				'woocommerce_my_account_edit_address_title' => 'content',
				'woocommerce_lost_password_message' => 'content',
				'woocommerce_reset_password_message' => 'content',
				'woocommerce_lost_password_confirmation_message' => 'content',
				'woocommerce_my_account_my_address_description' => 'content',
				
				'woocommerce_my_account_my_downloads_title' => 'content',
				'woocommerce_available_download_count' => 'content',
				
				'woocommerce_return_to_shop_text' => 'content',
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
				'woocommerce_get_allowed_countries' => 'content',
				'woocommerce_template_single_excerpt' => 'content',
				'woocommerce_cart_item_name' => 'content',
				'gettext_woocommerce' => 'content',
				'woocommerce_cart_item_quantity' => [__CLASS__, 'fix_quantity'],
		//		'woocommerce_cart_item_product' => 'objects',
		//		'woocommerce_cart_item_price' => 'content',
		//		'woocommerce_cart_item_subtotal' => 'content'
			));
			
			return $filters;
		}
		
		public static function fix_quantity ($data) {
			
			if(isset($data['product_name'])) {
				$data['product_name'] = Transliteration_Controller::get()->transliterate_no_html($data['product_name']);
			}
			
			return $data;
		}
	}
endif;
