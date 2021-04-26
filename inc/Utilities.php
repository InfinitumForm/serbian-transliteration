<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * Main global classes with active hooks
 * @since     1.0.0
 * @verson    1.0.0
 */
if(!class_exists('Serbian_Transliteration_Utilities')) :
class Serbian_Transliteration_Utilities{

	/*
	 * Plugin mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function plugin_mode($mode=NULL){
		$modes = array(
			'standard'	=> __('Standard mode (content, themes, plugins, translations, menu)', RSTR_NAME),
			'advanced'	=> __('Advanced mode (content, widgets, themes, plugins, translations, menu‚ permalinks, media)', RSTR_NAME),
			'forced'	=> __('Forced transliteration (everything)', RSTR_NAME)
		);

		if(RSTR_WOOCOMMERCE) {
			$modes = array_merge($modes, array(
				'woocommerce'	=> __('Only WooCommerce (It bypasses all other transliterations and focuses only on WooCommerce)', RSTR_NAME)
			));
		}

		$modes = apply_filters('rstr_plugin_mode', $modes);

		if($mode){
			if(isset($modes[$mode])) {
				return $modes[$mode];
			}

			return false;
		}

		return $modes;
	}

	/*
	 * Transliteration mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function transliteration_mode($mode=NULL){
		$modes = array(
			'none'			=> __('Transliteration disabled', RSTR_NAME),
			'cyr_to_lat'	=> __('Cyrillic to Latin', RSTR_NAME),
			'lat_to_cyr'	=> __('Latin to Cyrillic', RSTR_NAME)
		);

		$modes = apply_filters('rstr_transliteration_mode', $modes);

		if($mode && isset($modes[$mode])){
			return $modes[$mode];
		}

		return $modes;
	}

	/*
	 * Decode content
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function decode($content, $flag=ENT_NOQUOTES){
		if (filter_var($content, FILTER_VALIDATE_URL)) {
			$content = rawurldecode($content);
		} else {
			$content = htmlspecialchars_decode($content, $flag);
			$content = html_entity_decode($content, $flag);
			$content = strtr($content, array_flip(get_html_translation_table(HTML_ENTITIES, $flag)));
		}
		return $content;
	}

	/*
	 * Check is already cyrillic
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function already_cyrillic(){
        return in_array(Serbian_Transliteration::__instance()->get_locale(), apply_filters('rstr_already_cyrillic', array('sr_RS','mk_MK', 'bel', 'bg_BG', 'ru_RU', 'sah', 'uk', 'kk', 'el'))) !== false;
	}

	/*
	 * Check is latin letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function is_lat($c){
		return preg_match_all('/[\p{Latin}]+/ui', strip_tags($c, '')) !== false;
	}

	/*
	 * Check is cyrillic letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function is_cyr($c){
		return preg_match_all('/[\p{Cyrillic}]+/ui', strip_tags($c, '')) !== false;
	}

	/*
	* Get transliteration mode and load important classes
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function mode($options=false){

		if(empty($options)) $options = get_rstr_option();
		if(is_null($options)) return false;

		$mode = ucfirst($options['mode']);
		$class_require = "Serbian_Transliteration_Mode_{$mode}";
		$path_require = "mode/{$mode}";

		$path = apply_filters('rstr/mode/path', RSTR_INC, $class_require, $options['mode']);

		if(!class_exists($class_require))
		{
			if(file_exists($path . "/{$path_require}.php"))
			{
				include_once $path . "/{$path_require}.php";
				if(class_exists($class_require)){
					return $class_require;
				} else {
					throw new Exception(sprintf('The class "$1%s" does not exist or is not correctly defined on the line %2%d', $mode_class, (__LINE__-2)));
				}
			} else {
				throw new Exception(sprintf('The file at location "$1%s" does not exist or has a permissions problem.', $path . "/{$path_require}.php"));
			}
		}
		else
		{
			return $class_require;
		}

		// Clear memory
		$class_require = $path_require = $path = $mode = NULL;

		return false;
	}

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
			$script = $mode = get_rstr_option('transliteration-mode', 'none');
			$site_script = get_rstr_option('site-script', 'lat');
			$first_visit = get_rstr_option('first-visit-mode', 'auto');

			if(isset($_COOKIE['rstr_script']) && !empty($_COOKIE['rstr_script']))
			{
				if($_COOKIE['rstr_script'] == 'lat') {
					$script = 'lat';
					if($mode == 'cyr_to_lat'){
						$script =  'cyr_to_lat';
					}
				} else if($_COOKIE['rstr_script'] == 'cyr') {
					$script = 'cyr';
					if($mode == 'lat_to_cyr'){
						$script =  'lat_to_cyr';
					}
				}
			}
			else
			{
				if($first_visit == 'lat') {
					$script = 'lat';
					self::setcookie('lat');
					if($mode == 'cyr_to_lat'){
						$script =  'cyr_to_lat';
					}
				} else if($first_visit == 'cyr') {
					$script = 'cyr';
					self::setcookie('cyr');
					if($mode == 'lat_to_cyr'){
						$script =  'lat_to_cyr';
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

	/*
	 * Get current URL
	 * @since     1.0.9
	 * @verson    1.0.0
	*/
	public static function get_current_url()
	{
		global $wp;
		return add_query_arg( array(), home_url( $wp->request ) );
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

	/*
	* Set current transliteration script
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function set_current_script(){
		$url_selector = get_rstr_option('url-selector', 'rstr');

		if(isset($_REQUEST[$url_selector]))
		{
			if(in_array($_REQUEST[$url_selector], apply_filters('rstr/allowed_script', array('cyr', 'lat')), true) !== false)
			{
				self::setcookie($_REQUEST[$url_selector]);
				$parse_url = self::parse_url();
				$url = remove_query_arg($url_selector, $parse_url['url']);

				if(get_rstr_option('cache-support', 'yes') == 'yes') {
					$url = add_query_arg('_rstr_nocache', uniqid($url_selector . mt_rand(100,999)), $url);
				}

				if(wp_safe_redirect($url)) {
					if(function_exists('nocache_headers')) nocache_headers();
					exit;
				}
			}
		}
		return false;
	}

	/*
	 * Set cookie
	 * @since     1.0.10
	 * @verson    1.0.0
	*/
	public static function setcookie ($val){
		if( !headers_sent() ) {
			global $rstr_cache;

			setcookie( 'rstr_script', $val, (time()+YEAR_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
			$rstr_cache->delete('get_current_script');

			if(get_rstr_option('cache-support', 'yes') == 'yes') {
				self::cache_flush();
			}
		}
	}

	/*
	 * Flush Cache
	 * @verson    1.0.1
	*/
	public static function cache_flush () {
		global $post, $user, $w3_plugin_totalcache;

		// Standard cache
		header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Set nocache headers
		if(function_exists('nocache_headers')) {
			nocache_headers();
		}

		// Flush WP cache
		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}

		// W3 Total Cache
		if (function_exists('w3tc_flush_all')) {
			w3tc_flush_all();
		} else if( $w3_plugin_totalcache ) {
			$w3_plugin_totalcache->flush_all();
		}

		// WP Fastest Cache
		if (function_exists('wpfc_clear_all_cache')) {
			wpfc_clear_all_cache(true);
		}

		// WP Rocket
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}

		// WP Super Cache
		if(function_exists( 'prune_super_cache' ) && function_exists( 'get_supercache_dir' )) {
			prune_super_cache( get_supercache_dir(), true );
		}

		// Cache Enabler.
		if (function_exists( 'clear_site_cache' )) {
			clear_site_cache();
		}

		// Clean stanrad WP cache
		if($post && function_exists('clean_post_cache')) {
			clean_post_cache( $post );
		}

		// Comet Cache
		if(class_exists('comet_cache') && method_exists('comet_cache', 'clear')) {
			comet_cache::clear();
		}

		// Clean user cache
		if($user && function_exists('clean_user_cache')) {
			clean_user_cache( $user );
		}
	}

	/**
	* Get current page ID
	* @autor    Ivijan-Stefan Stipic
	* @since    1.0.7
	* @version  2.0.0
	******************************************************************/
	public static function get_page_ID(){
		global $post, $rstr_cache;

		if($current_page_id = $rstr_cache->get('current_page_id')){
			return $current_page_id;
		}

		if($id = self::get_page_ID__private__wp_query())
			return $rstr_cache->set('current_page_id', $id);
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

	// Get page ID by wp_query
	protected static function get_page_ID__private__wp_query(){
		global $wp_query;
		return ((!is_null($wp_query) && isset($wp_query->post) && isset($wp_query->post->ID) && !empty($wp_query->post->ID)) ? $wp_query->post->ID : false);
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

	/*
	* Register language script
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function attachment_taxonomies() {
		if(!taxonomy_exists('rstr-script'))
		{
			register_taxonomy( 'rstr-script', array( 'attachment' ), array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => _x( 'Script', 'Language script', RSTR_NAME ),
					'singular_name'     => _x( 'Script', 'Language script', RSTR_NAME ),
					'search_items'      => __( 'Search by Script', RSTR_NAME ),
					'all_items'         => __( 'All Scripts', RSTR_NAME ),
					'parent_item'       => __( 'Parent Script', RSTR_NAME ),
					'parent_item_colon' => __( 'Parent Script:', RSTR_NAME ),
					'edit_item'         => __( 'Edit Script', RSTR_NAME ),
					'update_item'       => __( 'Update Script', RSTR_NAME ),
					'add_new_item'      => __( 'Add New Script', RSTR_NAME ),
					'new_item_name'     => __( 'New Script Name', RSTR_NAME ),
					'menu_name'         => __( 'Script', RSTR_NAME ),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'publicly_queryable'=> false,
				'show_in_menu'		=> false,
				'show_in_nav_menus'	=> false,
				'show_in_rest'		=> false,
				'show_tagcloud'		=> false,
				'show_in_quick_edit'=> false
			) );
		}
	}
}
endif;
