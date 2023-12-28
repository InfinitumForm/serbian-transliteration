<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * Main global classes with active hooks
 * @since     1.0.0
 * @verson    1.0.0
 */
if(!class_exists('Serbian_Transliteration_Utilities', false)) :
class Serbian_Transliteration_Utilities{

	public static function plugin_default_options () {
		return apply_filters('rstr_plugin_default_options', array(
			'site-script'				=>	'cyr',
			'transliteration-mode'		=>	'cyr_to_lat',
			'mode'						=>	'advanced',
			'avoid-admin'				=>	'no',
			'allow-cyrillic-usernames'	=>	'no',
			'media-transliteration'		=>	'yes',
			'media-delimiter'			=>	'-',
			'permalink-transliteration'	=>	'yes',
			'cache-support'				=>  'yes',
			'exclude-latin-words'		=>	'WordPress',
			'exclude-cyrillic-words'	=>	'',
			'enable-search'				=>	'yes',
			'search-mode'       		=>	'auto',
			'enable-alternate-links'	=>	'no',
			'first-visit-mode'			=>	'auto',
			'enable-rss'				=>	'yes',
			'fix-diacritics'			=>	'yes',
			'url-selector'				=>	'rstr',
			'language-scheme'			=>	( 
				in_array(
					self::get_locale(),
					array_keys(Serbian_Transliteration_Transliterating::registered_languages())
				) ? self::get_locale() : 'auto'
			),
			'enable-body-class'			=>	'yes',
			'force-widgets'				=>	'no',
			'force-email-transliteration' => 'no',
			'force-ajax-calls' 			=> 'no',
			'disable-by-language'		=> ['en_US'=>'yes']
		));
	}
	
	public static function skip_transliteration(){
		return (isset($_REQUEST['rstr_skip']) && in_array($_REQUEST['rstr_skip'], array('true', true, 1, '1', 'yes')) !== false);
	}

	/*
	 * Plugin mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function plugin_mode($mode=NULL){
		$modes = array(
			'standard'	=> __('Standard mode (content, themes, plugins, translations, menu)', 'serbian-transliteration'),
			'advanced'	=> __('Advanced mode (content, widgets, themes, plugins, translations, menu‚ permalinks, media)', 'serbian-transliteration'),
			'forced'	=> __('Forced transliteration (everything)', 'serbian-transliteration')
		);

		if(RSTR_WOOCOMMERCE) {
			$modes = array_merge($modes, array(
				'woocommerce'	=> __('Only WooCommerce (It bypasses all other transliterations and focuses only on WooCommerce)', 'serbian-transliteration')
			));
		}
		
		if( defined('RSTR_DEBUG') && RSTR_DEBUG ) {
			$modes = array_merge($modes, array(
				'dev'	=> __('Dev Mode (Only for developers and testers)', 'serbian-transliteration')
			));
		}

		$modes = apply_filters('rstr_plugin_mode', $modes);

		if($mode){
			if(isset($modes[$mode])) {
				return $modes[$mode];
			}

			return [];
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
			'none'			=> __('Transliteration disabled', 'serbian-transliteration'),
			'cyr_to_lat'	=> __('Cyrillic to Latin', 'serbian-transliteration'),
			'lat_to_cyr'	=> __('Latin to Cyrillic', 'serbian-transliteration')
		);

		$locale = self::get_locale();

		if($locale == 'ar'){
			$modes['cyr_to_lat']= __('Arabic to Latin', 'serbian-transliteration');
			$modes['lat_to_cyr']= __('Latin to Arabic', 'serbian-transliteration');
		} else if($locale == 'hy'){
			$modes['cyr_to_lat']= __('Armenian to Latin', 'serbian-transliteration');
			$modes['lat_to_cyr']= __('Latin to Armenian', 'serbian-transliteration');
		}

		$modes = apply_filters('rstr_transliteration_mode', $modes);

		if($mode && isset($modes[$mode])){
			return $modes[$mode];
		}

		return $modes;
	}
	
	
	/*
	 * Exclude transliteration
	 * @return        bool
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function exclude_transliteration() : bool {
		static $exclude_transliteration;
		
		if( NULL !== $exclude_transliteration ) {
			return $exclude_transliteration;
		}
		
		$locale = self::get_locale();
		
		$exclude = get_rstr_option('disable-by-language', []);
		
		if( is_array($exclude) ) {
			foreach($exclude as $ex_locale => $confirm) {
				if( $locale === $ex_locale && $confirm == 'yes' ) {
					$exclude_transliteration = true;
					return true;
				}
			}
		}
		
		$exclude_transliteration = false;
		return false;
	}

	/*
	 * Get current locale
	 * @return        string if is $locale empty or bool if is provided $locale
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function get_locale($locale = NULL){
		if(empty($locale)){
			return Serbian_Transliteration::__instance()->get_locale();
		} else {
			return (Serbian_Transliteration::__instance()->get_locale() === $locale);
		}
	}

	/*
	 * Decode content
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function decode($content, $flag=ENT_NOQUOTES){
		if ( !empty($content) && is_string($content) && !is_numeric($content) && !is_array($content) && !is_object($content) ) {
			if (filter_var($content, FILTER_VALIDATE_URL)) {
				$content = rawurldecode($content);
			} else {
				$content = htmlspecialchars_decode($content, $flag);
				$content = html_entity_decode($content, $flag);
			//	$content = strtr($content, array_flip(get_html_translation_table(HTML_ENTITIES, $flag)));
			}
		}
		return $content;
	}

	/*
	 * Check is already cyrillic
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function already_cyrillic(){
        return in_array(self::get_locale(), apply_filters('rstr_already_cyrillic', array('sr_RS','mk_MK', 'bel', 'bg_BG', 'ru_RU', 'sah', 'uk', 'kk', 'el', 'ar', 'hy'))) !== false;
	}

	/*
	 * Check is latin letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function is_lat($c){
		return (preg_match_all('/[\p{Latin}]+/ui', strip_tags($c, '')) !== false);
	}

	/*
	 * Check is cyrillic letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function is_cyr($c){
		return (preg_match_all('/[\p{Cyrillic}]+/ui', strip_tags($c, '')) !== false);
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

		if(!class_exists($class_require, false))
		{
			if(file_exists($path . "/{$path_require}.php"))
			{
				if( !class_exists('Serbian_Transliteration_Mode', false) ) {
					include_once RSTR_INC . '/Mode.php';
				}
				
				/* Load plugin mode
				====================================*/
				include_once $path . "/{$path_require}.php";
				
				/* Load plugins support
				====================================*/
				Serbian_Transliteration_Plugins::includes();
				
				/* Load themes support
				====================================*/
				Serbian_Transliteration_Themes::includes();
				
				if(class_exists($class_require, false)){
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

		$script = Serbian_Transliteration_Cache::get('get_current_script');

		if(empty($script))
		{
			$script = $mode = get_rstr_option('transliteration-mode', 'none');
			$site_script = get_rstr_option('site-script', 'lat');
			$first_visit = get_rstr_option('first-visit-mode', 'auto');
			
			if(isset($_COOKIE['rstr_script']) && !empty($_COOKIE['rstr_script']))
			{
				if($_COOKIE['rstr_script'] == 'lat') {
					$script = 'cyr_to_lat';
					/*if($mode == 'cyr_to_lat'){
						$script =  'cyr_to_lat';
					}*/
				} else if($_COOKIE['rstr_script'] == 'cyr') {
					$script = 'lat_to_cyr';
					/*if($mode == 'lat_to_cyr'){
						$script =  'lat_to_cyr';
					}*/
				}
			}
			else
			{
				if($first_visit == 'lat') {
					$script = 'cyr_to_lat';
					self::setcookie('lat');
					/*if($mode == 'cyr_to_lat'){
						$script =  'cyr_to_lat';
					}*/
				} else if($first_visit == 'cyr') {
					$script = 'lat_to_cyr';
					self::setcookie('cyr');
					/*if($mode == 'lat_to_cyr'){
						$script =  'lat_to_cyr';
					}*/
				}
			}

			Serbian_Transliteration_Cache::set('get_current_script', $script);
		}

		return $script;
	}
	
	/*
	 * Check is plugin active
	 */
	public static function is_plugin_active($plugin)
	{
		static $active_plugins = [];
		
		if( !isset($active_plugins[$plugin]) ) {
			if(!function_exists('is_plugin_active')) {
				self::include_once( WP_ADMIN_DIR . '/includes/plugin.php' );
			}
			$active_plugins[$plugin] = is_plugin_active($plugin);
		}

		return $active_plugins[$plugin];
	}

	/*
	* Check is block editor screen
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function is_editor()
	{
		$is_editor = Serbian_Transliteration_Cache::get('is_editor');

		if( self::is_elementor_editor() ) {
			$is_editor = Serbian_Transliteration_Cache::set('is_editor', true);
		}
		
		if( self::is_oxygen_editor() ) {
			$is_editor = Serbian_Transliteration_Cache::set('is_editor', true);
		}

		if(NULL === $is_editor) {
			if (version_compare(get_bloginfo( 'version' ), '5.0', '>=')) {
				if(!function_exists('get_current_screen')){
					include_once ABSPATH  . '/wp-admin/includes/screen.php';
				}
				$get_current_screen = get_current_screen();
				if(is_callable(array($get_current_screen, 'is_block_editor')) && method_exists($get_current_screen, 'is_block_editor')) {
					$is_editor = Serbian_Transliteration_Cache::set('is_editor', $get_current_screen->is_block_editor());
				}
			} else {
				$is_editor = Serbian_Transliteration_Cache::set('is_editor', ( isset($_GET['action']) && isset($_GET['post']) && $_GET['action'] == 'edit' && is_numeric($_GET['post']) ) );
			}
		}

		return $is_editor;
	}
	
	/* 
	 * Check is in the Elementor editor mode
	 * @verson    1.0.0
	 */
	public static function is_elementor_editor(){
		if(
			(
				self::is_plugin_active('elementor/elementor.php') 
				&& ($_REQUEST['action'] ?? NULL) === 'elementor'
				&& is_numeric($_REQUEST['post'] ?? NULL)
			)
			|| preg_match('/^(elementor_(.*?))$/i', ($_REQUEST['action'] ?? ''))
		) {
			return true;
			
			// Deprecated
			//	return \Elementor\Plugin::$instance->editor->is_edit_mode();
		}
		
		return false;
	}
	
	/* 
	 * Check is in the Elementor preview mode
	 * @verson    1.0.0
	 */
	public static function is_elementor_preview(){
		if(
			!is_admin()
			&& (
				self::is_plugin_active('elementor/elementor.php') 
				&& ($_REQUEST['preview'] ?? NULL) == 'true'
				&& is_numeric($_REQUEST['page_id'] ?? NULL)
				&& is_numeric($_REQUEST['preview_id'] ?? NULL)
				&& !empty($_REQUEST['preview_nonce'] ?? NULL)
			) || preg_match('/^(elementor_(.*?))$/i', ($_REQUEST['action'] ?? ''))
		) {
			return true;
			
			// Deprecated
			//	return \Elementor\Plugin::$instance->preview->is_preview_mode();
		}
		
		return false;
	}
	
	/* 
	 * Check is in the Oxygen editor mode
	 * @verson    1.0.0
	 */
	public static function is_oxygen_editor(){
		if(
			self::is_plugin_active('oxygen/functions.php') 
			&& (
				($_REQUEST['ct_builder'] ?? NULL) == 'true'
				|| ($_REQUEST['ct_inner'] ?? NULL) == 'true'
				|| preg_match('/^((ct_|oxy_)(.*?))$/i', ($_REQUEST['action'] ?? ''))
			)
		) {
			return true;
		}
		
		return false;
	}

	/*
	 * Generate unique token
	 * @author    Ivijan-Stefan Stipic
	*/
	public static function generate_token($length=16){
		if(function_exists('openssl_random_pseudo_bytes') || function_exists('random_bytes'))
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
				return substr(str_rot13(bin2hex(random_bytes(ceil($length * 2)))), 0, $length);
			} else {
				return substr(str_rot13(bin2hex(openssl_random_pseudo_bytes(ceil($length * 2)))), 0, $length);
			}
		}
		else
		{
			return substr(str_replace(array('.',' ','_'),mt_rand(1000,9999),uniqid('t'.microtime())), 0, $length);
		}
	}

	/*
	 * Delete all plugin translations
	 * @return        bool
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function clear_plugin_translations(){
		$domain_path = [
			path_join( WP_LANG_DIR, 'plugins' ) . '/' . RSTR_NAME . '-*.{po,mo}',
			WP_LANG_DIR . '/' . RSTR_NAME . '-*.{po,mo}'
		];
		
		$i = 0;
		
		foreach ($domain_path as $pattern) {
			foreach (glob($pattern, GLOB_BRACE) as $file) {
				if (file_exists($file)) {
					unlink($file);
					++$i;
				}
			}
		}
		
		return ($i > 0) ? true : false;
	}

	/*
	 * Delete all plugin transients and cached options
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function clear_plugin_cache(){
		global $wpdb;
		
		$locale = Serbian_Transliteration_Transliterating::__init()->get_locale();
		
		if(Serbian_Transliteration_DB_Cache::get(RSTR_NAME . "-skip-words-{$locale}")) {
			Serbian_Transliteration_DB_Cache::delete(RSTR_NAME . "-skip-words-{$locale}");
		}
		if(Serbian_Transliteration_DB_Cache::get(RSTR_NAME . "-diacritical-words-{$locale}")) {
			Serbian_Transliteration_DB_Cache::delete(RSTR_NAME . "-diacritical-words-{$locale}");
		}
		if(Serbian_Transliteration_DB_Cache::get(RSTR_NAME . '-locales')) {
			Serbian_Transliteration_DB_Cache::delete(RSTR_NAME . '-locales');
		}
		if(get_option(RSTR_NAME . '-html-tags')) {
			delete_option(RSTR_NAME . '-html-tags');
		}
		if(get_option(RSTR_NAME . '-version')) {
			delete_option(RSTR_NAME . '-version');
		}
		
		if($wpdb) {
			$RSTR_NAME = RSTR_NAME;
			$wpdb->query("DELETE FROM `{$wpdb->options}` WHERE `{$wpdb->options}`.`option_name` REGEXP '^_transient_(.*)?{$RSTR_NAME}(.*|$)'");
		}
	}

	/*
	 * Return plugin informations
	 * @return        array/object
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function plugin_info($fields = array()) {
        if ( is_admin() ) {
			
			$hash = md5(serialize($fields));
			
			if($plugin_data = Serbian_Transliteration_Cache::get("plugin_info_{$hash}")){
				return $plugin_data;
			}
			
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

			return Serbian_Transliteration_Cache::set("plugin_info_{$hash}", $plugin_data);
		}
    }

	/*
	 * Get current URL
	 * @since     1.0.9
	 * @verson    2.0.0
	*/
	public static function get_current_url()
	{
		global $wp;
		
		$current_page = Serbian_Transliteration_Cache::get('get_current_url');
		
		// Get page by path
		if(!$current_page) {
			$current_page =  get_page_by_path($wp->request);
			// Get post by ID
			if(!$current_page) {
				$current_page = get_post(isset($wp->query_vars['p']) ? absint($wp->query_vars['p']) : NULL);
				// Get page by ID
				if(!$current_page) {
					$current_page = get_post(isset($wp->query_vars['page_id']) ? absint($wp->query_vars['page_id']) : NULL);
					// Get post by date/time
					if(
						!$current_page
						&& (
							isset($wp->query_vars['name'])
							|| isset($wp->query_vars['year'])
							|| isset($wp->query_vars['monthnum'])
							|| isset($wp->query_vars['day'])
							|| isset($wp->query_vars['hour'])
							|| isset($wp->query_vars['minute'])
							|| isset($wp->query_vars['second'])
						)
					) {
						
						$attr = array();
						if(isset($wp->query_vars['name'])) {
							$attr['name'] = $wp->query_vars['name'];
						}
						
						if(
							isset($wp->query_vars['year'])
							|| isset($wp->query_vars['monthnum'])
							|| isset($wp->query_vars['day'])
							|| isset($wp->query_vars['hour'])
							|| isset($wp->query_vars['minute'])
							|| isset($wp->query_vars['second'])
						){
							$attr['date_query'] = array();
							
							if(isset($wp->query_vars['year'])){
								$attr['date_query']['year']= $wp->query_vars['year'];
							}
							
							if(isset($wp->query_vars['monthnum'])){
								$attr['date_query']['month']= $wp->query_vars['monthnum'];
							}
							
							if(isset($wp->query_vars['day'])){
								$attr['date_query']['day']= $wp->query_vars['day'];
							}
							
							if(isset($wp->query_vars['hour'])){
								$attr['date_query']['hour']= $wp->query_vars['hour'];
							}
							
							if(isset($wp->query_vars['minute'])){
								$attr['date_query']['minute']= $wp->query_vars['minute'];
							}
							
							if(isset($wp->query_vars['second'])){
								$attr['date_query']['second']= $wp->query_vars['second'];
							}
						}
						
						$page = get_posts($attr);
						if($page) {
							$current_page = $page[0];
						}

						// Get page by GET pharam
						if(!$current_page) {
							$current_page = get_post(isset($_GET['page_id']) ? absint($_GET['page_id']) : NULL);
							// Get post by GET pharam
							if(!$current_page) {
								$current_page = get_post(isset($_GET['p']) ? absint($_GET['p']) : NULL);
							}
						}
					}
				}
			}			
		}

		return Serbian_Transliteration_Cache::set('get_current_url', $current_page);
	}

	/**
	 * Parse URL
	 * @since     1.2.2
	 * @verson    1.0.0
	 */
	public static function parse_url(){
		if(!Serbian_Transliteration_Cache::get('url_parsed')) {
			$http = 'http'.( self::is_ssl() ?'s':'');
			$domain = preg_replace('%:/{3,}%i','://',rtrim($http,'/').'://'.$_SERVER['HTTP_HOST']);
			$domain = rtrim($domain,'/');
			$url = preg_replace('%:/{3,}%i','://',$domain.'/'.(isset($_SERVER['REQUEST_URI']) && !empty( $_SERVER['REQUEST_URI'] ) ? ltrim($_SERVER['REQUEST_URI'], '/'): ''));

			Serbian_Transliteration_Cache::set('url_parsed', array(
				'method'	=>	$http,
				'home_fold'	=>	str_replace($domain,'',home_url()),
				'url'		=>	$url,
				'domain'	=>	$domain,
			));
		}

		return Serbian_Transliteration_Cache::get('url_parsed');
	}

	/*
	 * CHECK IS SSL
	 * @return	true/false
	 */
	public static function is_ssl($url = false)
	{

		$ssl = Serbian_Transliteration_Cache::get('is_ssl');

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
				$ssl = Serbian_Transliteration_Cache::set('is_ssl', true);
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
					if(function_exists('nocache_headers')) nocache_headers();
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

			setcookie( 'rstr_script', $val, (time()+YEAR_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
			Serbian_Transliteration_Cache::delete('get_current_script');

			if(get_rstr_option('cache-support', 'yes') == 'yes') {
				self::cache_flush();
			}

			return true;
		}

		return false;
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
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate, proxy-revalidate");
		header('Clear-Site-Data: "cache", "storage", "executionContexts"');
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
		if(class_exists('comet_cache', false) && method_exists('comet_cache', 'clear')) {
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
		global $post;

		if($current_page_id = Serbian_Transliteration_Cache::get('current_page_id')){
			return $current_page_id;
		}

		if($id = self::get_page_ID__private__wp_query())
			return Serbian_Transliteration_Cache::set('current_page_id', $id);
		else if($id = self::get_page_ID__private__get_the_id())
			return Serbian_Transliteration_Cache::set('current_page_id', $id);
		else if(!is_null($post) && isset($post->ID) && !empty($post->ID))
			return Serbian_Transliteration_Cache::set('current_page_id', $post->ID);
		else if($post = self::get_page_ID__private__GET_post())
			return Serbian_Transliteration_Cache::set('current_page_id', $post);
		else if($p = self::get_page_ID__private__GET_p())
			return Serbian_Transliteration_Cache::set('current_page_id', $p);
		else if($page_id = self::get_page_ID__private__GET_page_id())
			return Serbian_Transliteration_Cache::set('current_page_id', $page_id);
		else if($id = self::get_page_ID__private__query())
			return $id;
		else if($id = self::get_page_ID__private__page_for_posts())
			return Serbian_Transliteration_Cache::set('current_page_id', get_option( 'page_for_posts' ));

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
		
		if( is_admin() ) {
			return false;
		}
		
		global $wpdb;
		$actual_link = rtrim($_SERVER['REQUEST_URI'], '/');
		$parts = self::explode('/', ($actual_link??''));
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
					return Serbian_Transliteration_Cache::set('current_page_id', absint($post_id));
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
					'name'              => _x( 'Script', 'Language script', 'serbian-transliteration' ),
					'singular_name'     => _x( 'Script', 'Language script', 'serbian-transliteration' ),
					'search_items'      => __( 'Search by Script', 'serbian-transliteration' ),
					'all_items'         => __( 'All Scripts', 'serbian-transliteration' ),
					'parent_item'       => __( 'Parent Script', 'serbian-transliteration' ),
					'parent_item_colon' => __( 'Parent Script:', 'serbian-transliteration' ),
					'edit_item'         => __( 'Edit Script', 'serbian-transliteration' ),
					'update_item'       => __( 'Update Script', 'serbian-transliteration' ),
					'add_new_item'      => __( 'Add New Script', 'serbian-transliteration' ),
					'new_item_name'     => __( 'New Script Name', 'serbian-transliteration' ),
					'menu_name'         => __( 'Script', 'serbian-transliteration' ),
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

	/*
	* PHP Wrapper for explode — Split a string by a string
	* @since     1.0.9
	* @verson    1.0.0
	* @url       https://www.php.net/manual/en/function.explode.php
	*/
	public static function explode($separator , $string , $limit = PHP_INT_MAX ){
		$string = explode($separator, ($string??''), $limit);
		$string = array_map('trim', $string);
		$string = array_filter($string);
		return $string;
	}
	
	/*
	* Normalize latin string and remove special characters
	* @since     1.6.7
	*/
	public static function normalize_latin_string($str){
		$map = apply_filters('rstr/utilities/normalize_latin_string', array(
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Ă'=>'A', 'Ā'=>'A', 'Ą'=>'A', 'Æ'=>'A', 'Ǽ'=>'A',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'ă'=>'a', 'ā'=>'a', 'ą'=>'a', 'æ'=>'a', 'ǽ'=>'a',

			'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',

			'Ç'=>'C', 'Č'=>'C', 'Ć'=>'C', 'Ĉ'=>'C', 'Ċ'=>'C',
			'ç'=>'c', 'č'=>'c', 'ć'=>'c', 'ĉ'=>'c', 'ċ'=>'c',

			'Đ'=>'Dj', 'Ď'=>'D',
			'đ'=>'dj', 'ď'=>'d',

			'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ĕ'=>'E', 'Ē'=>'E', 'Ę'=>'E', 'Ė'=>'E',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'ę'=>'e', 'ė'=>'e',

			'Ĝ'=>'G', 'Ğ'=>'G', 'Ġ'=>'G', 'Ģ'=>'G',
			'ĝ'=>'g', 'ğ'=>'g', 'ġ'=>'g', 'ģ'=>'g',

			'Ĥ'=>'H', 'Ħ'=>'H',
			'ĥ'=>'h', 'ħ'=>'h',

			'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'Ĩ'=>'I', 'Ī'=>'I', 'Ĭ'=>'I', 'Į'=>'I',
			'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'į'=>'i', 'ĩ'=>'i', 'ī'=>'i', 'ĭ'=>'i', 'ı'=>'i',

			'Ĵ'=>'J',
			'ĵ'=>'j',

			'Ķ'=>'K', 'Ƙ'=>'K',
			'ķ'=>'k', 'ĸ'=>'k',

			'Ĺ'=>'L', 'Ļ'=>'L', 'Ľ'=>'L', 'Ŀ'=>'L', 'Ł'=>'L',
			'ĺ'=>'l', 'ļ'=>'l', 'ľ'=>'l', 'ŀ'=>'l', 'ł'=>'l',

			'Ñ'=>'N', 'Ń'=>'N', 'Ň'=>'N', 'Ņ'=>'N', 'Ŋ'=>'N',
			'ñ'=>'n', 'ń'=>'n', 'ň'=>'n', 'ņ'=>'n', 'ŋ'=>'n', 'ŉ'=>'n',

			'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ō'=>'O', 'Ŏ'=>'O', 'Ő'=>'O', 'Œ'=>'O',
			'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ō'=>'o', 'ŏ'=>'o', 'ő'=>'o', 'œ'=>'o', 'ð'=>'o',

			'Ŕ'=>'R', 'Ř'=>'R',
			'ŕ'=>'r', 'ř'=>'r', 'ŗ'=>'r',

			'Š'=>'S', 'Ŝ'=>'S', 'Ś'=>'S', 'Ş'=>'S',
			'š'=>'s', 'ŝ'=>'s', 'ś'=>'s', 'ş'=>'s',

			'Ŧ'=>'T', 'Ţ'=>'T', 'Ť'=>'T',
			'ŧ'=>'t', 'ţ'=>'t', 'ť'=>'t',

			'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ũ'=>'U', 'Ū'=>'U', 'Ŭ'=>'U', 'Ů'=>'U', 'Ű'=>'U', 'Ų'=>'U',
			'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ũ'=>'u', 'ū'=>'u', 'ŭ'=>'u', 'ů'=>'u', 'ű'=>'u', 'ų'=>'u',

			'Ŵ'=>'W', 'Ẁ'=>'W', 'Ẃ'=>'W', 'Ẅ'=>'W',
			'ŵ'=>'w', 'ẁ'=>'w', 'ẃ'=>'w', 'ẅ'=>'w',

			'Ý'=>'Y', 'Ÿ'=>'Y', 'Ŷ'=>'Y',
			'ý'=>'y', 'ÿ'=>'y', 'ŷ'=>'y',

			'Ž'=>'Z', 'Ź'=>'Z', 'Ż'=>'Z',
			'ž'=>'z', 'ź'=>'z', 'ż'=>'z',

			'“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '•'=>'-', '…'=>'...', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>__(' degrees ', 'serbian-transliteration'),
			'¼'=>' 1/4 ', '½'=>' 1/2 ', '¾'=>' 3/4 ', '⅓'=>' 1/3 ', '⅔'=>' 2/3 ', '⅛'=>' 1/8 ', '⅜'=>' 3/8 ', '⅝'=>' 5/8 ', '⅞'=>' 7/8 ',
			'÷'=>__(' divided by ', 'serbian-transliteration'), '×'=>__(' times ', 'serbian-transliteration'), '±'=>__(' plus-minus ', 'serbian-transliteration'), '√'=>__(' square root ', 'serbian-transliteration'),
			'∞'=>__(' infinity ', 'serbian-transliteration'), '≈'=>__(' almost equal to ', 'serbian-transliteration'), '≠'=>__(' not equal to ', 'serbian-transliteration'), 
			'≡'=>__(' identical to ', 'serbian-transliteration'), '≤'=>__(' less than or equal to ', 'serbian-transliteration'), '≥'=>__(' greater than or equal to ', 'serbian-transliteration'),
			'←'=>__(' left ', 'serbian-transliteration'), '→'=>__(' right ', 'serbian-transliteration'), '↑'=>__(' up ', 'serbian-transliteration'), '↓'=>__(' down ', 'serbian-transliteration'),
			'↔'=>__(' left and right ', 'serbian-transliteration'), '↕'=>__(' up and down ', 'serbian-transliteration'), '℅'=>__(' care of ', 'serbian-transliteration'), 
			'℮' => __(' estimated ', 'serbian-transliteration'), 'Ω'=>__(' ohm ', 'serbian-transliteration'), '♀'=>__(' female ', 'serbian-transliteration'), '♂'=>__(' male ', 'serbian-transliteration'),
			'©'=>__(' Copyright ', 'serbian-transliteration'), '®'=>__(' Registered ', 'serbian-transliteration'), '™' =>__(' Trademark ', 'serbian-transliteration'),
		), $str);

		return strtr($str, $map);
	}
}
endif;
