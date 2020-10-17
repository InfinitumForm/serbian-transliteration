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
class Serbian_Transliteration_Mode_Woocommerce extends Serbian_Transliteration
{
	private $options;
	
	public static function filters ($options=array()) {
		if(empty($options)) $options = get_rstr_option();
		
		$filters = array();
		// WooCommerce
		if (RSTR_WOOCOMMERCE) {
			$filters = array_merge($filters, array(
				'woocommerce_product_single_add_to_cart_text' => 'content',
				'woocommerce_email_footer_text' => 'content',
				'woocommerce_get_availability_text' => 'content',
				'woocommerce_get_price_html_from_text' => 'content',
				'woocommerce_order_button_text' => 'content',
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
				'woocommerce_stock_html' => 'content',
				'woocommerce_single_product_image_thumbnail_html' => 'content',
				'woocommerce_variable_price_html' => 'content',
				'woocommerce_variable_empty_price_html' => 'content'
			));
		}
		asort($filters);
		
		return $filters;
	}

	function __construct($options){
		if($options !== false)
		{
			$this->options = $options;
			
			$filters = self::filters($this->options);
			$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->options);

			if(!is_admin())
			{
				foreach($filters as $filter=>$function) $this->add_filter($filter, $function, 9999999, 1);
			}
		}
	}
	
	public function content ($content='') {
		if(empty($content)) return $content;
		
		
		if(is_array($content))
		{
			$content = $this->title_parts($content);
		}
		else if(is_string($content) && !is_numeric($content))
		{
				
			switch($this->get_current_script($this->options))
			{
				case 'cyr_to_lat' :
					$content = $this->cyr_to_lat($content);
					break;
					
				case 'lat_to_cyr' :
					$content = $this->lat_to_cyr($content);			
					break;
			}
		}
		return $content;
	}
}
endif;