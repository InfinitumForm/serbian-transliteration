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

			$filters = array(
				'ngettext'              => 'content',
				'ngettext_with_context' => 'content',
				'gettext_with_context'  => 'content',
				'gettext'               => 'content',
				'date_i18n'             => 'content',
				'the_title'             => 'content',
				'wp_title'              => 'content',
				'option_blogname' 		=> 'content',
				'option_blogdescription'=> 'content',
				'document_title_parts'  => 'title_parts',
				'wp_get_object_terms'   => 'transliteration_wp_terms',
				'load_script_translations' => 'transliteration_json_content',
				'pre_load_script_translations' => 'transliteration_json_content'
			);
			
			// WooCommerce fix
			if(RSTR_WOOCOMMERCE) {
				$filters = array_merge($filters, array(
					'woocommerce_currency_symbol' => 'content',
					'woocommerce_currencies' => 'content'
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

					foreach($filters as $key=>$function){
						$this->add_filter($key, $function, (PHP_INT_MAX-1), 1);
					}
				}
			}
		}

		public function transliteration_json_content( $json_content ) {
			
			if( empty($json_content) ) {
				return $json_content;
			}
			
			$content = json_decode( $json_content, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return $json_content;
			}

			if ( is_array( $content ) && isset( $content['locale_data']['messages'] ) && is_array( $content['locale_data']['messages'] ) ) {
				foreach ( $content['locale_data']['messages'] as $key => $messages ) {
					if ( ! $key || ! is_array( $messages ) ) {
						continue;
					}

					foreach ( $messages as $key2 => $message ) {
						$message                                             = $this->cyr_to_lat( $message );
						$content['locale_data']['messages'][ $key ][ $key2 ] = $message;
					}
				}
			}

			return wp_json_encode( $content );
		}

		public function content( $content = '' ) {
			if ( empty( $content ) ) {
				return '';
			}

			if ( is_array( $content ) ) {
				$content = $this->title_parts( $content );
			} else if ( is_string( $content ) && ! is_numeric( $content ) ) {
				$content = $this->cyr_to_lat( $content );
			}
			
			if(NULL === $content) {
				$content = '';
			}

			return $content;
		}

		public function title_parts( $titles = array() ) {
			foreach ( $titles as $key => $val ) {
				if ( is_string( $val ) && ! is_numeric( $val ) ) {
					$titles[ $key ] = $this->cyr_to_lat( $titles[ $key ] );
				}
			}
			return $titles;
		}

		/*
		 * Transliterate WP terms
		 * @contributor    Ivijan-Stefan Stipić
		 * @version        2.0.0
		**/
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

			return $wp_terms;
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
