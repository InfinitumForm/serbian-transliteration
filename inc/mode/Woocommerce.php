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
			'get_the_terms'			=> 'transliteration_wp_terms',//Sydney, Blocksy, Colormag
			'wp_get_object_terms' 	=> 'transliteration_wp_terms', //Phlox
			'wp_mail'				=> 'wp_mail'
		);

		return $filters;
	}

	public function __construct() {
		$filters = self::filters($this->get_options());
		$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->get_options());
		$filters = apply_filters('rstr/transliteration/exclude/filters/woocommerce', $filters, $this->get_options());

		$mode = new Serbian_Transliteration_Mode();

		if (!is_admin()) {
			foreach ($filters as $key => $method) {
				if (is_string($method)) {
					$target_method = method_exists($mode, $method) ? [$mode, $method] : (method_exists($this, $method) ? [$this, $method] : null);
					if ($target_method) {
						$this->add_filter($key, $target_method, (PHP_INT_MAX - 1), 1);
					}
				}
			}
		}
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