<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * Main global classes with active hooks
 * @since     1.0.0
 * @verson    1.0.0
 */
if(!class_exists('Serbian_Transliteration_Utilities')) :
class Serbian_Transliteration_Utilities{
	
	/* 
	* Get current transliteration script
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function get_current_script(){
		global $rstr_cache;
		
		$script = $rstr_cache->get('get_current_script');
		
		if(empty($script))
		{
			$options=get_rstr_option();
			
			$script = (isset($options['transliteration-mode']) && !empty($options['transliteration-mode']) ? $options['transliteration-mode'] : 'none');
			
			if(isset($_COOKIE['rstr_script']) && !empty($_COOKIE['rstr_script']))
			{
				if($_COOKIE['rstr_script'] == 'lat') {
					$script = 'cyr_to_lat';
					if(isset($options['transliteration-mode']) && $options['site-script'] == 'lat'){
						$script = 'lat';
					}
				} else if($_COOKIE['rstr_script'] == 'cyr') {
					$script = 'lat_to_cyr';
					if(isset($options['transliteration-mode']) && $options['site-script'] == 'cyr'){
						$script =  'cyr';
					}
				}
			}
			
			$rstr_cache->set('get_current_script', $script);
		}
		
		return $script;
	}
	
	/* 
	* Check is block editor screen
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function is_editor()
	{
		global $rstr_cache;
		
		$is_editor = $rstr_cache->get('is_editor');
		
		if(empty($is_editor)) {
			if (version_compare(get_bloginfo( 'version' ), '5.0', '>=')) {
				if(!function_exists('get_current_screen')){
					include_once ABSPATH  . '/wp-admin/includes/screen.php';
				}
				$get_current_screen = get_current_screen();
				if(is_callable(array($get_current_screen, 'is_block_editor')) && method_exists($get_current_screen, 'is_block_editor')) {
					$is_editor = $rstr_cache->set('is_editor', $get_current_screen->is_block_editor());
				}
			} else {
				$is_editor = $rstr_cache->set('is_editor', ( isset($_GET['action']) && isset($_GET['post']) && $_GET['action'] == 'edit' && is_numeric($_GET['post']) ) );
			}
		}
		
		return $is_editor;
	}
	
	/* 
	 * Generate unique token
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function generate_token($length=16){
		if(function_exists('openssl_random_pseudo_bytes') || function_exists('random_bytes'))
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>='))
				return substr(str_rot13(bin2hex(random_bytes(ceil($length * 2)))), 0, $length);
			else
				return substr(str_rot13(bin2hex(openssl_random_pseudo_bytes(ceil($length * 2)))), 0, $length);
		}
		else
		{
			return substr(str_replace(array('.',' ','_'),mt_rand(1000,9999),uniqid('t'.microtime())), 0, $length);
		}
	}
	
	/*
	 * Delete all plugin ransients and cached options
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function clear_plugin_cache(){
		if(get_transient(RSTR_NAME . '-skip-words')) {
			delete_transient(RSTR_NAME . '-skip-words');
		}
		if(get_transient(RSTR_NAME . '-diacritical-words')) {
			delete_transient(RSTR_NAME . '-diacritical-words');
		}
		if(get_transient(RSTR_NAME . '-locales')) {
			delete_transient(RSTR_NAME . '-locales');
		}
		if(get_option(RSTR_NAME . '-html-tags')) {
			delete_option(RSTR_NAME . '-html-tags');
		}
		if(get_option(RSTR_NAME . '-version')) {
			delete_option(RSTR_NAME . '-version');
		}
	}
	
	/*
	 * Return plugin informations
	 * @return        array/object
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function plugin_info($fields = array()) {
        if ( is_admin() ) {
			if ( ! function_exists( 'plugins_api' ) ) {
				include_once( WP_ADMIN_DIR . '/includes/plugin-install.php' );
			}
			/** Prepare our query */
			//donate_link
			//versions
			$plugin_data = plugins_api( 'plugin_information', array(
				'slug' => RSTR_NAME,
				'fields' => array_merge(array(
					'active_installs' => false,           // rounded int
					'added' => false,                     // date
					'author' => false,                    // a href html
					'author_block_count' => false,        // int
					'author_block_rating' => false,       // int
					'author_profile' => false,            // url
					'banners' => false,                   // array( [low], [high] )
					'compatibility' => false,            // empty array?
					'contributors' => false,              // array( array( [profile], [avatar], [display_name] )
					'description' => false,              // string
					'donate_link' => false,               // url
					'download_link' => false,             // url
					'downloaded' => false,               // int
					// 'group' => false,                 // n/a 
					'homepage' => false,                  // url
					'icons' => false,                    // array( [1x] url, [2x] url )
					'last_updated' => false,              // datetime
					'name' => false,                      // string
					'num_ratings' => false,               // int
					'rating' => false,                    // int
					'ratings' => false,                   // array( [5..0] )
					'requires' => false,                  // version string
					'requires_php' => false,              // version string
					// 'reviews' => false,               // n/a, part of 'sections'
					'screenshots' => false,               // array( array( [src],  ) )
					'sections' => false,                  // array( [description], [installation], [changelog], [reviews], ...)
					'short_description' => false,        // string
					'slug' => false,                      // string
					'support_threads' => false,           // int
					'support_threads_resolved' => false,  // int
					'tags' => false,                      // array( )
					'tested' => false,                    // version string
					'version' => false,                   // version string
					'versions' => false,                  // array( [version] url )
				), $fields)
			));
		 
			return $plugin_data;
		}
    }
	
	/**
	 * Parse URL
	 * @since     1.2.2
	 * @verson    1.0.0
	 */
	public static function parse_url(){
		global $rstr_cache;
		if(!$rstr_cache->get('url_parsed')) {
			$http = 'http'.( self::is_ssl() ?'s':'');
			$domain = preg_replace('%:/{3,}%i','://',rtrim($http,'/').'://'.$_SERVER['HTTP_HOST']);
			$domain = rtrim($domain,'/');
			$url = preg_replace('%:/{3,}%i','://',$domain.'/'.(isset($_SERVER['REQUEST_URI']) && !empty( $_SERVER['REQUEST_URI'] ) ? ltrim($_SERVER['REQUEST_URI'], '/'): ''));
				
			$rstr_cache->set('url_parsed', array(
				'method'	=>	$http,
				'home_fold'	=>	str_replace($domain,'',home_url()),
				'url'		=>	$url,
				'domain'	=>	$domain,
			));
		}
		
		return $rstr_cache->get('url_parsed');
	}
	
	/*
	 * CHECK IS SSL
	 * @return	true/false
	 */
	public static function is_ssl($url = false)
	{
		global $rstr_cache;
		
		$ssl = $rstr_cache->get('is_ssl');
		
		if($url !== false && is_string($url)) {
			return (preg_match('/(https|ftps)/Ui', $url) !== false);
		} else if(empty($ssl)) {
			if(
				( is_admin() && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ===true )
				|| (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
				|| (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
				|| (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
				|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
				|| (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
				|| (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
			) {
				$ssl = $rstr_cache->set('is_ssl', true);
			}
		}
		return $ssl;
	}
	
	/**
	* Get current page ID
	* @autor    Ivijan-Stefan Stipic
	* @since    1.0.7
	* @version  2.0.0
	******************************************************************/
	public static function get_page_ID(){
		global $post, $wp_query, $rstr_cache;
		
		if($current_page_id = $rstr_cache->get('current_page_id')){
			return $current_page_id;
		}
		
		if(!is_null($wp_query) && isset($wp_query->post) && isset($wp_query->post->ID) && !empty($wp_query->post->ID))
			return $rstr_cache->set('current_page_id', $wp_query->post->ID);
		else if($id = self::get_page_ID__private__get_the_id())
			return $rstr_cache->set('current_page_id', $id);
		else if(!is_null($post) && isset($post->ID) && !empty($post->ID))
			return $rstr_cache->set('current_page_id', $post->ID);
		else if($post = self::get_page_ID__private__GET_post())
			return $rstr_cache->set('current_page_id', $post);
		else if($p = self::get_page_ID__private__GET_p())
			return $rstr_cache->set('current_page_id', $p);
		else if($page_id = self::get_page_ID__private__GET_page_id())
			return $rstr_cache->set('current_page_id', $page_id);
		else if(!is_admin() && $id = self::get_page_ID__private__query())
			return $id;
		else if($id = self::get_page_ID__private__page_for_posts())
			return $rstr_cache->set('current_page_id', get_option( 'page_for_posts' ));

		return false;
	}
	
	// Get page ID by using get_the_id() function
	protected static function get_page_ID__private__get_the_id(){
		if(function_exists('get_the_id'))
		{
			if($id = get_the_id()) return $id;
		}
		return false;
	}
	
	// Get page ID by GET[post] in edit mode
	protected static function get_page_ID__private__GET_post(){
		return ((isset($_GET['action']) && sanitize_text_field($_GET['action']) == 'edit') && (isset($_GET['post']) && is_numeric($_GET['post'])) ? absint($_GET['post']) : false);
	}
	
	// Get page ID by GET[page_id]
	protected static function get_page_ID__private__GET_page_id(){
		return ((isset($_GET['page_id']) && is_numeric($_GET['page_id']))  ? absint($_GET['page_id']) : false);
	}
	
	// Get page ID by GET[p]
	protected static function get_page_ID__private__GET_p(){
		return ((isset($_GET['p']) && is_numeric($_GET['p']))  ? absint($_GET['p']) : false);
	}
	
	// Get page ID by OPTION[page_for_posts]
	protected static function get_page_ID__private__page_for_posts(){
		$page_for_posts = get_option( 'page_for_posts' );
		return (!is_admin() && 'page' == get_option( 'show_on_front' ) && $page_for_posts ? absint($page_for_posts) : false);
	}
	
	// Get page ID by mySQL query
	protected static function get_page_ID__private__query(){
		global $wpdb, $rstr_cache;
		$actual_link = rtrim($_SERVER['REQUEST_URI'], '/');
		$parts = explode('/', $actual_link);
		if(!empty($parts))
		{
			$slug = end($parts);
			if(!empty($slug))
			{
				if($post_id = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT ID FROM {$wpdb->posts} 
						WHERE 
							`post_status` = %s
						AND
							`post_name` = %s
						AND
							TRIM(`post_name`) <> ''
						LIMIT 1",
						'publish',
						sanitize_title($slug)
					)
				))
				{
					return $rstr_cache->set('current_page_id', absint($post_id));
				}
			}
		}
		
		return false;
	}
	
	/**
	* END Get current page ID
	*****************************************************************/
}
endif;