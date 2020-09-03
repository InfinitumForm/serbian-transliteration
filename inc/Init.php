<?php
/**
 * Init class
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
 if(!class_exists('Serbian_Transliteration_Init') && class_exists('Serbian_Transliteration')) :
final class Serbian_Transliteration_Init extends Serbian_Transliteration {
	
	private static $instance = NULL;
	
	/**
	 * Get singleton instance of global class
	 * @since     7.4.0
	 * @version   7.4.0
	 */
	private static function get_instance()
	{
		if( NULL === self::$instance )
		{
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	public static function run () {
		// Load instance
		$inst = self::get_instance();
		
		if( is_admin() )
		{
			// Load settings page
			include_once RSTR_INC . '/Settings.php';
			new Serbian_Transliteration_Settings();
		}
		
		// Load options
		$options = get_option( RSTR_NAME );
		
		// Load shortcodes
		include_once RSTR_INC . '/Shortcodes.php';
		new Serbian_Transliteration_Shortcodes($options);
		
		// Initialize plugin mode
		if(isset($options['mode']) && $options['mode'] && in_array( $options['mode'], array_keys($inst->plugin_mode()), true ) !== false)
		{
			if($options['transliteration-mode'] != 'none')
			{
				$mode = ucfirst($options['mode']);
				$class_require = "Serbian_Transliteration_Mode_{$mode}";
				$path_require = "Mode_{$mode}";
				$path = apply_filters('serbian_transliteration_class_mode_path', RSTR_INC, $class_require, $options['mode']);
				
				if(file_exists($path . "/{$path_require}.php"))
				{
					include_once $path . "/{$path_require}.php";
					if(class_exists($class_require)){
						new $class_require($options);
					}
				}
				
				// Clear memory
				$class_require = $path_require = $path = $mode = NULL;
			}
			
			/* Media upload transliteration
			=========================================*/
			if(isset($options['media-transliteration']) && $options['media-transliteration'] == 'yes'){
				$inst->add_filter('wp_handle_upload_prefilter', 'upload_filter', 9999999, 1);
			}
			
			/* Permalink transliteration
			=========================================*/
			if(isset($options['permalink-transliteration']) && $options['permalink-transliteration'] == 'yes' && ($inst->get_locale() == 'sr_RS' && !get_option('ser_cyr_to_lat_slug'))){
				$inst->add_filter('sanitize_title', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('the_permalink', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('wp_unique_post_slug', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('permalink_manager_filter_default_post_uri', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('permalink_manager_filter_default_term_uri', 'force_permalink_to_latin', 9999999, 1);
				$inst->add_filter('wp_insert_post_data', 'force_permalink_to_latin_on_save', 9999999, 2);
			}
			
			/* WordPress search transliteration
			=========================================*/
			if(isset( $options['enable-search'] ) && $options['enable-search'] == 'yes')
			{
				include_once RSTR_INC . '/Search.php';
				new Serbian_Transliteration_Search($options);
			}
			
			/* WordPress exlude words
			=========================================*/
			if(isset($options['exclude-latin-words']) && !empty($options['exclude-latin-words']))
			{
				add_filter('serbian_transliteration_cyr_exclude_list', function($list) use ($options){
					$array = array();
					if($split = preg_split('/[\n|]/', $options['exclude-latin-words']))
					{
						$split = array_map('trim',$split);
						$split = array_filter($split);
						if(!empty($split) && is_array($split))
						{
							$array = $split;
						}
					}
					return array_merge($list, $array);
				});
			}
			
			if(isset($options['exclude-cyrillic-words']) && !empty($options['exclude-cyrillic-words']))
			{
				add_filter('serbian_transliteration_lat_exclude_list', function($list) use ($options){
					$array = array();
					if($split = preg_split('/[\n|]/', $options['exclude-cyrillic-words']))
					{
						$split = array_map('trim',$split);
						$split = array_filter($split);
						if(!empty($split) && is_array($split))
						{
							$array = $split;
						}
					}
					return array_merge($list, $array);
				});
			}
		}
	}
}
endif;