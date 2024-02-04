<?php if ( ! defined( 'WPINC' ) )	die( "Don't mess with us." );
/**
 * Admin Menu Transliteration
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if ( ! class_exists( 'Serbian_Transliteration_Mode_Admin' ) ) :
class Serbian_Transliteration_Mode_Admin extends Serbian_Transliteration {

	/* Run this script */
	public static function run() {
		$class = self::class;
		$instance = Serbian_Transliteration_Cache::get($class);
		if ( !$instance ) {
			$instance = Serbian_Transliteration_Cache::set($class, new self());
		}
		return $instance;
	}
	
	public static function filters($options = []) {
		global $pagenow;

		if (!is_admin()) {
			return [];
		}
		
		$options = empty($options) ? get_rstr_option() : $options;
		
		$filters = [
			'ngettext'              => 'content__force_lat',
			'ngettext_with_context' => 'content__force_lat',
			'gettext_with_context'  => 'content__force_lat',
			'gettext'               => 'content__force_lat',
			'date_i18n'             => 'content__force_lat',
			'the_title'             => 'content__force_lat',
			'wp_title'              => 'content__force_lat',
			'option_blogname' 		=> 'content__force_lat',
			'option_blogdescription'=> 'content__force_lat',
			'document_title_parts'  => 'title_parts',
			'wp_get_object_terms'   => 'transliteration_wp_terms',
			'load_script_translations' => 'transliteration_json_content',
			'pre_load_script_translations' => 'transliteration_json_content'
		];

		// WooCommerce fix
		if (RSTR_WOOCOMMERCE) {
			$filters['woocommerce_currency_symbol'] = 'content__force_lat';
			$filters['woocommerce_currencies'] = 'content__force_lat';
		}

		// Bug fix on the settings page
		if (in_array($pagenow, ['options-general.php', 'options.php'], true) && empty($_GET['page'])) {
			unset($filters['option_blogname'], $filters['option_blogdescription']);
		}

		return $filters;
	}


	public function __construct() {
		if (is_admin() && $this->get_option('avoid-admin') === 'no') {
			$filters = apply_filters('rstr/transliteration/exclude/filters/admin', self::filters($this->get_options()), $this->get_options());
			$mode = new Serbian_Transliteration_Mode();
			$args = 1;
			
			foreach ($filters as $key => $method) {
				
				do_action('rstr/transliteration/filter/arguments/admin/before', $key, $method);
				
				if( is_array($method) ) {
					$this->add_filter($key, $method, (PHP_INT_MAX - 1), $args);
				} else if (is_string($method)) {
					$target = method_exists($mode, $method) ? $mode : (method_exists($this, $method) ? $this : null);
					if ($target) {
						$this->add_filter($key, [$target, $method], (PHP_INT_MAX - 1), $args);
					}
				}
				
				do_action('rstr/transliteration/filter/arguments/admin/after', $key, $method);
				
			}
		}
	}

	/**
	 * @param $locale
	 *
	 * @return string
	 * @author Slobodan Pantovic
	 */
	public function current_user_locale($locale) {
		if ( $user_locale = get_user_meta(get_current_user_id(), 'locale', true) ) {
			$available_languages = get_available_languages(RSTR_ROOT . '/languages');
			$cleaned_languages = array_map(function($language) {
				return str_replace(RSTR_NAME . '-', '', $language);
			}, $available_languages);

			if (in_array($user_locale, $cleaned_languages)) {
				return $user_locale;
			}
		}

		return $locale;
	}

}
endif;
