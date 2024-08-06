<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Mode_Admin', false) ) : class Transliteration_Mode_Admin {
    
	// Mode ID
	const MODE = 'admin';
	
    /*
	 * The main constructor
	 */
    public function __construct() {
		
    }
	
	/*
	 * Get current instance
	 */
	private static $instance = NULL;
	public static function get() {
		if( NULL === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/*
	 * Get available filters for this mode
	 */
	public function filters() {
		
		global $pagenow;

		if (!is_admin()) {
			return [];
		}
		
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
    
} endif;