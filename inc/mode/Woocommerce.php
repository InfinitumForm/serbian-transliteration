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
			$class = self::class;
			$instance = $rstr_cache->get($class);
			if ( !$instance ) {
				$instance = $rstr_cache->set($class, new self());
			}
			return $instance;
		}

		public static function filters ($options=array()) {
			if(empty($options)) $options = get_rstr_option();

			$filters = array(
				'get_the_terms'			=> 'transliteration_wp_terms',//Sydney, Blocksy, Colormag
				'wp_get_object_terms' 	=> 'transliteration_wp_terms', //Phlox
			);

			return $filters;
		}

		function __construct(){
			$filters = self::filters($this->get_options());
			$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->get_options());

			if(!is_admin())
			{
				foreach($filters as $key=>$function){
					$this->add_filter($key, $function, (PHP_INT_MAX-1), 1);
				}

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

		public function transliteration_wp_terms($wp_terms)
		{
			if (!empty($wp_terms))
			{
				if(is_array($wp_terms))
				{
					foreach($wp_terms as $i => $term)
					{
						if(is_object($term) && ((isset($term->name) && !empty($term->name)) || (isset($term->description) && !empty($term->description))))
						{
							if(in_array($term->taxonomy, array('product_cat', 'product_type', 'product_tag'), true) === false || strpos($term->taxonomy, 'pa_')===false)
							{
								switch(Serbian_Transliteration_Utilities::get_current_script())
								{
									case 'cyr_to_lat' :
										if(isset($term->name) && !empty($term->name)){
											$wp_terms[$i]->name = $this->cyr_to_lat($term->name);
										}
										if(isset($term->description) && !empty($term->description)){
											$wp_terms[$i]->description = $this->cyr_to_lat($term->description);
										}
										break;
									case 'lat_to_cyr' :
										if(isset($term->name) && !empty($term->name)){
											$wp_terms[$i]->name = $this->lat_to_cyr($term->name);
										}
										if(isset($term->description) && !empty($term->description)){
											$wp_terms[$i]->description = $this->lat_to_cyr($term->description);
										}
										break;
								}
							}
						}
					}
				}
			}
			return $wp_terms;
		}

		function rss_output_buffer_start() {
			ob_start(NULL, 0, PHP_OUTPUT_HANDLER_REMOVABLE);
		}

		function rss_output_buffer_end() {
			$output = ob_get_clean();

			$output = $this->transliterate_text($output);

			echo $output;
		}
	}
endif;
