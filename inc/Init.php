<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
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
	 * @since     1.0.0
	 * @version   1.0.0
	 */
	private static function get_instance()
	{
		if( NULL === self::$instance )
		{
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	/**
	 * Get singleton instance of global class
	 * @since     1.0.9
	 * @version   1.0.0
	 */
	public function remove_menu_page() {
		global $submenu;
		
		if(isset($submenu['upload.php']) && is_array($submenu['upload.php']))
		{
			foreach($submenu['upload.php'] as $parent=>$locations){
				if(is_array($locations))
				{
					foreach($locations as $i=>$value)
					{
						if(strpos($value, 'rstr-script') !== false) unset($submenu['upload.php'][$parent]);
					}
				}
			}
		}
	}
	
	public function set_cookie($options){
		if( !(isset($_COOKIE['rstr_script'])) )
		{
			if($options['transliteration-mode'] == 'cyr_to_lat') {
				$this->setcookie('lat');
			} else if($options['transliteration-mode'] == 'lat_to_cyr') {
				$this->setcookie('cyr');
			}
		}
	}
	
	public function admin_head(){ ?>
<style>#rstr-script-adder{display:none !important;}</style>
	<?php }
	
	public static function run () {
		// Load instance
		$inst = self::get_instance();
		
		// Register taxonomy
		parent::attachment_taxonomies();		
		
		if( is_admin() )
		{
			// Remove admin menu pages
			add_action('admin_menu', array($inst, 'remove_menu_page'));
			// Add soem scripts
			add_action('admin_head', array($inst, 'admin_head'));
			// Load settings page
			include_once RSTR_INC . '/Settings.php';
			$Serbian_Transliteration_Settings = new Serbian_Transliteration_Settings();
			new Serbian_Transliteration_Settings_Sidebar( $Serbian_Transliteration_Settings );
			new Serbian_Transliteration_Settings_Content( $Serbian_Transliteration_Settings );
		}
		else
		{
			$inst->set_current_script();
		}
		
		// Load options
		$options = get_rstr_option();
		
		// Load shortcodes
		include_once RSTR_INC . '/Shortcodes.php';
		new Serbian_Transliteration_Shortcodes($options);
		
		// Initialize plugin mode
		if(isset($options['mode']) && $options['mode'] && in_array( $options['mode'], array_keys($inst->plugin_mode()), true ) !== false)
		{			
			if($options['transliteration-mode'] != 'none')
			{
				// Display alternate links
				if(defined('RSTR_ALTERNATE_LINKS') && RSTR_ALTERNATE_LINKS) {
					$inst->add_action('wp_head', 'alternate_links', 1);
				}
				
				// Set cookie
				$inst->set_cookie($options);
		
				// Include mode class				
				if($mode_class = $inst->mode($options)) {
					new $mode_class($options);
				}
			}

			/* Media upload transliteration
			=========================================*/
			if(isset($options['media-transliteration']) && $options['media-transliteration'] == 'yes'){
				$inst->add_filter('wp_handle_upload_prefilter', 'upload_prefilter', 9999999, 1);
				$inst->add_filter( 'sanitize_file_name', 'sanitize_file_name', 99 );
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
				add_filter('rstr/init/exclude/cyr', function($list) use ($options){
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
				add_filter('rstr/init/exclude/lat', function($list) use ($options){
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
			
			/* Allows to create users with usernames containing Cyrillic characters
			=========================================*/
			if(isset($options['allow-cyrillic-usernames']) && $options['allow-cyrillic-usernames'] == 'yes')
			{
				add_filter('sanitize_user', function ($username, $raw_username, $strict) {
					$username = wp_strip_all_tags( $raw_username );
					$username = remove_accents( $username );

					// Kill octets
					$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
					$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

					// If strict, reduce to ASCII and Cyrillic characters for max portability.
					if ( $strict ){
						$username = preg_replace( '|[^a-zа-я0-9 _.\-@]|iu', '', $username );
					}
					$username = trim( $username );

					// Consolidate contiguous whitespace
					$username = preg_replace( '|\s+|', ' ', $username );

					return $username;
				}, 10, 3);
			}
		}
	}
}
endif;