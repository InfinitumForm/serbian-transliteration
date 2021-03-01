<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Woocommerce (Deprecated mode)
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
		/* Run this script */
		public static function run() {
			global $rstr_cache;
			if ( !$rstr_cache->get('Serbian_Transliteration_Mode_Woocommerce') ) {
				$rstr_cache->set('Serbian_Transliteration_Mode_Woocommerce', new self());
			}
			return $rstr_cache->get('Serbian_Transliteration_Mode_Woocommerce');
		}
		
		public static function filters ($options=array()) {
			if(empty($options)) $options = get_rstr_option();
			
			$filters = array();
			
			return $filters;
		}

		function __construct(){
			$filters = self::filters($this->get_options());
			$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->get_options());

			if(!is_admin())
			{
				foreach($filters as $filter=>$function) $this->add_filter($filter, $function, (PHP_INT_MAX-1), 1);
				
				if(get_rstr_option('enable-rss', 'no') == 'yes')
				{						
					$this->add_action('rss_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('rss_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));
					
					$this->add_action('rss2_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('rss2_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));
					
					$this->add_action('rdf_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('rdf_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));
					
					$this->add_action('atom_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('atom_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));
				}
				
				if(get_rstr_option('force-widgets', 'no') == 'yes')
				{
					$this->add_action('dynamic_sidebar_before', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('dynamic_sidebar_after', 'rss_output_buffer_end', (PHP_INT_MAX-1));
				}
				
			}
		}
		
		function rss_output_buffer_start() {
			ob_start(NULL, 0, PHP_OUTPUT_HANDLER_REMOVABLE);
		}
		
		function rss_output_buffer_end() {
			$output = ob_get_clean();

			switch($this->get_current_script($this->get_options()))
			{
				case 'cyr_to_lat' :
					$output = $this->cyr_to_lat($output);
					break;
					
				case 'lat_to_cyr' :
					$output = $this->lat_to_cyr($output);
					break;
			}

			echo $output;
		}
	}
endif;