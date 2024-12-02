<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Admin {
	use Transliteration__Cache;
    
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
	public static function get() {
		return self::cached_static('instance', function(){
			return new self();
		});
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
		//	'gettext_with_context'  => 'content__force_lat',
			'gettext'               => 'content__force_lat',
			'date_i18n'             => 'content__force_lat',
		//	'the_title'             => 'content__force_lat',
			'wp_title'              => 'content__force_lat',
			'option_blogname' 		=> 'content__force_lat',
			'option_blogdescription'=> 'content__force_lat',
			'document_title_parts'  => 'title_parts',
			'wp_get_object_terms'   => 'transliteration_wp_terms',
			'load_script_translations' => 'transliteration_json_content',
			'pre_load_script_translations' => 'transliteration_json_content',
			'admin_menu' => [__CLASS__, 'transliterate_admin_menu'],
			'manage_pages_columns' => [__CLASS__, 'transliterate_pages_columns'],
			'display_post_states' => [__CLASS__, 'transliterate_pages_columns'],
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
	
	public static function transliterate_admin_menu(){
		global $menu, $submenu;
		if($menu) {
			foreach ($menu as $key => $menu_item){
				foreach ($menu_item as $key2 => $menu_item_item){
					$menu[$key][$key2] = Transliteration_Mode::get()->content__force_lat($menu_item_item);
				}
			}
		}
		
		if($submenu) {
			foreach ($submenu as $key => $menu_item){
				foreach ($menu_item as $key2 => $menu_item_item){
					if( isset($submenu[$key][$key2][0]) ) {
						$submenu[$key][$key2][0] = Transliteration_Mode::get()->content__force_lat($submenu[$key][$key2][0]);
					}
				}
			}
		}
	}
	
	public static function transliterate_pages_columns($columns){
		foreach ($columns as $key => $col){
			$columns[$key] = Transliteration_Mode::get()->content__force_lat($col);
		}
		return $columns;
	}
    
}