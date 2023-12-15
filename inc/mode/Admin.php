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

		public static function filters( $options = array() ) {
			global $pagenow;

			if ( empty( $options ) ) {
				$options = get_rstr_option();
			}
			
			if ( !is_admin() ) {
				return [];
			}

			$filters = array(
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
			);
			
			// WooCommerce fix
			if(RSTR_WOOCOMMERCE) {
				$filters = array_merge($filters, array(
					'woocommerce_currency_symbol' => 'content__force_lat',
					'woocommerce_currencies' => 'content__force_lat'
				));
			}

			// Bug fix on the settings page
			if( in_array($pagenow, array('options-general.php', 'options.php'), true) !== false && !(isset($_GET['page'])) ){
				unset($filters['option_blogname']);
				unset($filters['option_blogdescription']);
			}

			return $filters;
		}

		public function __construct() {
			if ( is_admin() ) {

				if ( $this->get_option('avoid-admin') === 'no' ) {
					$filters = self::filters( $this->get_options() );
					$filters = apply_filters( 'rstr/transliteration/exclude/filters/admin', $filters, $this->get_options() );
					
					$mode = new Serbian_Transliteration_Mode();
					
					foreach($filters as $key=>$method){
						if( is_string($method) ) {
							if( method_exists($mode, $method) ) {
								$this->add_filter($key, [$mode, $method], (PHP_INT_MAX-1), 1);
							} else if( method_exists($this, $method) ) {
								$this->add_filter($key, [$this, $method], (PHP_INT_MAX-1), 1);
							}
						}
					}
				}
			}
		}
		

		/**
		 * @param $locale
		 *
		 * @return string
		 * @author Slobodan Pantovic
		 */
		public function current_user_locale( $locale ) {
			$available_languages = get_available_languages( RSTR_ROOT . '/languages' );
			foreach ( $available_languages as $key => &$language ) {
				$language = str_replace( RSTR_NAME . '-', '', $language );
			}
			$current_user_ID = get_current_user_id();
			$user_meta       = get_user_meta( $current_user_ID );
			if ( is_array( $user_meta ) && isset( $user_meta['locale'][0] ) && ! empty( $user_meta['locale'][0] ) ) {
				$locale = get_user_locale( $current_user_ID );
				if ( in_array( $locale, $available_languages ) ) {
					return $locale;
				}
			}

			return $locale;
		}
	}
endif;
