<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Utilities', false) ) : class Transliteration_Utilities {
	
	/*
	 * Registered languages
	 * @since     1.4.3
	 * @verson    1.0.0
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function registered_languages(){
		return apply_filters('rstr_registered_languages', [
			'sr_RS'  => __('Serbian', 'serbian-transliteration'),
			'bs_BA'  => __('Bosnian', 'serbian-transliteration'),
			'cnr'    => __('Montenegrin', 'serbian-transliteration'),
			'ru_RU'  => __('Russian', 'serbian-transliteration'),
			'bel'    => __('Belarusian', 'serbian-transliteration'),
			'bg_BG'  => __('Bulgarian', 'serbian-transliteration'),
			'mk_MK'  => __('Macedoanian', 'serbian-transliteration'),
			'uk'     => __('Ukrainian', 'serbian-transliteration'),
			'kk'     => __('Kazakh', 'serbian-transliteration'),
			'tg'     => __('Tajik', 'serbian-transliteration'),
			'kir'    => __('Kyrgyz', 'serbian-transliteration'),
			'mn'     => __('Mongolian', 'serbian-transliteration'),
			'ba'     => __('Bashkir', 'serbian-transliteration'),
			'uz_UZ'  => __('Uzbek', 'serbian-transliteration'),
			'ka_GE'  => __('Georgian', 'serbian-transliteration'),
			'el'     => __('Greek', 'serbian-transliteration'),
			'hy'     => __('Armenian', 'serbian-transliteration'),
			'ar'     => __('Arabic', 'serbian-transliteration')
		]);
	}
	
	public static function plugin_default_options () {
		return apply_filters('rstr_plugin_default_options', [
			'site-script'					=> 'cyr',
			'transliteration-mode'			=> 'cyr_to_lat',
			'mode'							=> 'light',
			'avoid-admin'					=> 'no',
			'allow-cyrillic-usernames'		=> 'no',
			'media-transliteration'			=> 'yes',
			'media-delimiter'				=> '-',
			'permalink-transliteration'		=> 'yes',
			'cache-support'					=> self::has_cache_plugin() ? 'yes' : 'no',
			'exclude-latin-words'			=> 'WordPress',
			'exclude-cyrillic-words'		=> '',
			'enable-search'					=> 'yes',
			'search-mode'       			=> 'auto',
			'enable-alternate-links'		=> 'no',
			'first-visit-mode'				=> 'lat',
			'enable-rss'					=> 'yes',
			'fix-diacritics'				=> 'yes',
			'url-selector'					=> 'rstr',
			'language-scheme'				=> ( 
				in_array(
					self::get_locale(),
					array_keys(self::registered_languages())
				) ? self::get_locale() : 'auto'
			),
			'enable-body-class'				=> 'yes',
			'force-widgets'					=> 'no',
			'force-email-transliteration' 	=> 'no',
			'force-ajax-calls' 				=> 'no',
			'force-rest-api'				=> 'yes',
			'disable-by-language'			=> ['en_US'=>'yes'],
			'disable-theme-support'			=> 'no'
		]);
	}
	
	/*
	 * Skip transliteration
	 * @return        bool
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function skip_transliteration() : bool {
		// Static variable for caching the result
		static $result = null;

		// Return cached result if already computed
		if ($result !== null) {
			return $result;
		}

		// Compute the result for the first time
		$result = (isset($_REQUEST['rstr_skip']) && in_array(is_string($_REQUEST['rstr_skip']) ? strtolower($_REQUEST['rstr_skip']) : $_REQUEST['rstr_skip'], ['true', true, 1, '1', 'yes'], true) !== false);

		return $result;
	}
	
	/*
	 * Plugin mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function plugin_mode($mode=NULL){
		static $modes = [];
		
		if( !empty($modes) ) {
			return $modes;
		}
		
		$modes = [
			'light'		=> __('Light mode (basic parts of the site)', 'serbian-transliteration'),
			'standard'	=> __('Standard mode (content, themes, plugins, translations, menu)', 'serbian-transliteration'),
			'advanced'	=> __('Advanced mode (content, widgets, themes, plugins, translations, menu‚ permalinks, media)', 'serbian-transliteration'),
			'forced'	=> __('Forced transliteration (everything)', 'serbian-transliteration')
		];

		if(RSTR_WOOCOMMERCE) {
			$modes = array_merge($modes, [
				'woocommerce'	=> __('Only WooCommerce (It bypasses all other transliterations and focuses only on WooCommerce)', 'serbian-transliteration')
			]);
		}
		
		if( defined('RSTR_DEBUG') && RSTR_DEBUG ) {
			$modes = array_merge($modes, [
				'dev'	=> __('Dev Mode (Only for developers and testers)', 'serbian-transliteration')
			]);
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
	 * Get locale
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function get_locale($locale = NULL) {
		static $get_locale = NULL;
		
		if( !function_exists('is_user_logged_in') ) {
			include_once ABSPATH . WPINC . '/pluggable.php';
		}
		
		$language_scheme = get_rstr_option('language-scheme', 'auto');
		if ($language_scheme !== 'auto') {
			return $language_scheme;
		}

		if (empty($get_locale)) {
			$get_locale = function_exists('pll_current_language') ? pll_current_language('locale') : get_locale();
			
			if (is_user_logged_in() && empty($get_locale)) {
				$get_locale = get_user_locale( wp_get_current_user() );
			}
		}

		return empty($locale) ? $get_locale : ($get_locale === $locale);
	}
	
	/*
	 * Get list of available locales
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function get_locales( $needle = NULL ){
		$cache = Transliteration_Cache_DB::get('get_locales');

		if(empty($cache))
		{
			$file_name=apply_filters('rstr/init/libraries/file/locale', 'locale.lib');
			$cache = self::parse_library($file_name);
			
			if(!empty($cache)) {
				Transliteration_Cache_DB::set('get_locales', $cache, apply_filters('rstr/init/libraries/file/locale/transient', YEAR_IN_SECONDS));
			}
		}

		if($needle && is_array($cache)) {
			return (in_array($needle, $cache, true) !== false ? $needle : false);
		}

		return $cache;
	}
	
	
	/*
	* Read file with chunks with memory free
	* @since     1.6.7
	*/
	private static function read_file_chunks($path) {
		if($handle = fopen($path, 'r')) {
			while(!feof($handle)) {
				yield fgets($handle);
			}
			fclose($handle);
		} else {
			return false;
		}
	}
	
	/*
	 * Exclude transliteration
	 * @return        bool
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function exclude_transliteration() : bool {
		static $exclude_transliteration;

		if ($exclude_transliteration !== NULL) {
			return $exclude_transliteration;
		}

		$locale = self::get_locale();
		$exclude = get_rstr_option('disable-by-language', []);

		$exclude_transliteration = isset($exclude[$locale]) && $exclude[$locale] === 'yes';

		return $exclude_transliteration;
	}
	
	/*
	 * Check if it can be transliterated
	 * @param         string $content The content to check for transliteration
	 * @return        bool
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function can_transliterate($content) {
		// Quick exit for empty content
		if (empty($content)) {
			return apply_filters('rstr_can_transliterate', true, $content);
		}

		// Check if the content is of a type that does not support transliteration
		if (is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) {
			return apply_filters('rstr_can_transliterate', true, $content);
		}

		// Check for URL and Email
		if (self::is_url_or_email($content)) {
			return apply_filters('rstr_can_transliterate', true, $content);
		}

		// If none of the conditions are met, return true
		return apply_filters('rstr_can_transliterate', false, $content);
	}
	
	/*
	 * Is special type
	 */
	private function is_special_type($content) {
		if (empty($content)) {
			return true;
		}

		if (is_numeric($content) || is_bool($content)) {
			return true;
		}

		if (self::is_url_or_email($content)) {
			return true;
		}

		return false;
	}

	/*
	 * Check if string is URL or email
	 * @param         string $content The content to check if it's a URL or email
	 * @return        bool
	 * @uthor        Ivijan-Stefan Stipic
	 */
	public static function is_url_or_email($content) {
		$urlRegex = '/^((https?|s?ftp):)?\/\/([a-zA-Z0-9\-\._\+]+(:[a-zA-Z0-9\-\._]+)?@)?([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,})(:[0-9]+)?(\/[a-zA-Z0-9\-\._\%\&=\?\+\$\[\]\(\)\*\'\,\.\,\:]+)*\/?#?$/i';
		
		$emailRegex = '/^[a-zA-Z0-9\-\._\p{L}]+@[a-zA-Z0-9\-\._\p{L}]+\.[a-zA-Z]{2,}$/i';

		return !empty($content) && is_string($content) && strlen($content) > 10 && (preg_match($urlRegex, $content) || preg_match($emailRegex, $content));
	}

	
	/*
	 * Has cache plugin active
	 * @version    1.0.0
	 */
	public static function has_cache_plugin() {
		static $has_cache_plugin = null;

		if ($has_cache_plugin !== null) {
			return $has_cache_plugin;
		}

		global $w3_plugin_totalcache;

		$cache_checks = [
			['class_exists', '\LiteSpeed\Purge'],
			['has_action', 'litespeed_purge_all'],
			['function_exists', 'liteSpeed_purge_all'],
			['function_exists', 'w3tc_flush_all'],
			[$w3_plugin_totalcache],
			['function_exists', 'wpfc_clear_all_cache'],
			['function_exists', 'rocket_clean_domain'],
			['function_exists', 'prune_super_cache', 'get_supercache_dir'],
			['function_exists', 'clear_site_cache'],
			['class_exists', 'comet_cache', 'method_exists', 'comet_cache', 'clear'],
			['class_exists', 'PagelyCachePurge'],
			['function_exists', 'hyper_cache_clear'],
			['function_exists', 'simple_cache_flush'],
			['class_exists', 'autoptimizeCache', 'method_exists', 'autoptimizeCache', 'clearall'],
			['class_exists', 'WP_Optimize_Cache_Commands']
		];

		foreach ($cache_checks as $check) {
			if (count($check) === 1 && $check[0]) {
				$has_cache_plugin = true;
				break;
			} elseif (count($check) === 2 && in_array($check[0], ['function_exists', 'has_action']) && $check[0]($check[1])) {
				$has_cache_plugin = true;
				break;
			} elseif (count($check) === 2 && 'class_exists' === $check[0] && $check[0]($check[1], false)) {
				$has_cache_plugin = true;
				break;
			} elseif (count($check) === 3 && $check[0]($check[1]) && $check[0]($check[2])) {
				$has_cache_plugin = true;
				break;
			} elseif (count($check) === 4 && $check[0]($check[1], false) && $check[2]($check[3])) {
				$has_cache_plugin = true;
				break;
			}
		}

		if ($has_cache_plugin === null) {
			$has_cache_plugin = false;
		}

		return $has_cache_plugin;
	}
	
	/*
	 * Return plugin informations
	 * @return        array/object
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function plugin_info($fields = array()) {
		static $plugin_data = [];
		
        if ( is_admin() ) {
			
			$hash = 'rstr_plugin_info_'.hash('sha256', serialize($fields));
			
			if(array_key_exists($hash, $plugin_data)){
				return $plugin_data[$hash];
			}
			
			if ( ! function_exists( 'plugins_api' ) ) {
				include_once WP_ADMIN_DIR . '/includes/plugin-install.php';
			}
			/** Prepare our query */
			//donate_link
			//versions
			$plugin_data[$hash] = plugins_api( 'plugin_information', array(
				'slug' => RSTR_NAME,
				'fields' => array_merge(array(
					'active_installs' => false,           // rounded int
					'added' => false,                     // date
					'author' => false,                    // a href html
					'author_block_count' => false,        // int
					'author_block_rating' => false,       // int
					'author_profile' => false,            // url
					'banners' => false,                   // array( [low], [high] )
					'compatibility' => false,             // empty array?
					'contributors' => false,              // array( array( [profile], [avatar], [display_name] )
					'description' => false,               // string
					'donate_link' => false,               // url
					'download_link' => false,             // url
					'downloaded' => false,                // int
					// 'group' => false,                  // n/a
					'homepage' => false,                  // url
					'icons' => false,                     // array( [1x] url, [2x] url )
					'last_updated' => false,              // datetime
					'name' => false,                      // string
					'num_ratings' => false,               // int
					'rating' => false,                    // int
					'ratings' => false,                   // array( [5..0] )
					'requires' => false,                  // version string
					'requires_php' => false,              // version string
					// 'reviews' => false,                // n/a, part of 'sections'
					'screenshots' => false,               // array( array( [src],  ) )
					'sections' => false,                  // array( [description], [installation], [changelog], [reviews], ...)
					'short_description' => false,         // string
					'slug' => false,                      // string
					'support_threads' => false,           // int
					'support_threads_resolved' => false,  // int
					'tags' => false,                      // array( )
					'tested' => false,                    // version string
					'version' => false,                   // version string
					'versions' => false,                  // array( [version] url )
				), $fields)
			));

			return $plugin_data[$hash];
		}
    }
	
	/*
	 * Delete all plugin transients and cached options
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function clear_plugin_cache() {
		global $wpdb;
		
		$locale = self::get_locale();
		
		if(Transliteration_Cache_DB::get(RSTR_NAME . "-skip-words-{$locale}")) {
			Transliteration_Cache_DB::delete(RSTR_NAME . "-skip-words-{$locale}");
		}
		if(Transliteration_Cache_DB::get(RSTR_NAME . "-diacritical-words-{$locale}")) {
			Transliteration_Cache_DB::delete(RSTR_NAME . "-diacritical-words-{$locale}");
		}
		if(Transliteration_Cache_DB::get(RSTR_NAME . '-locales')) {
			Transliteration_Cache_DB::delete(RSTR_NAME . '-locales');
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
	 * Set cookie
	 * @since     1.0.10
	 * @verson    1.0.0
	*/
	public static function setcookie (string $val, int $expire = NULL) {
		if( !headers_sent() ) {
			if( !$expire ) {
				$expire = ( time() + YEAR_IN_SECONDS );
			}
			setcookie( 'rstr_script', $val, $expire, COOKIEPATH, COOKIE_DOMAIN );
			if(function_exists('nocache_headers')) nocache_headers();
			return true;
		}

		return false;
	}
	
	/*
	 * Get current script
	 * @since     1.0.10
	 * @verson    1.0.0
	 */
	public static function get_current_script() {
		
		// Get cached script
		static $script = NULL;
		if( $script ) {
			return $script;
		}
		
		// Cookie mode
		if( !empty( $_COOKIE['rstr_script'] ?? NULL ) ){
			switch( sanitize_text_field($_COOKIE['rstr_script']) ) {
				case 'lat':
					$script = 'lat';
					break;
				
				case 'cyr':
					$script = 'cyr';
					break;
			}
		}
		
		// Set new script
		$script = get_rstr_option('first-visit-mode', 'lat');
		
		return $script;
	}

	/*
	 * Flush Cache
	 * @verson    1.0.1
	 */
	protected static $cache_flush = false;
	public static function cache_flush () {
		
		// Flush must be fired only once
		if( self::$cache_flush ) {
			return true;
		}
		self::$cache_flush = true;
		
		// Let's enable all caches
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
		
		/*
		// Clean user cache
		if($user && function_exists('clean_user_cache')) {
			clean_user_cache( $user );
		}
		*/
		
		/*
		// Clean stanrad WP cache
		if($post && function_exists('clean_post_cache')) {
			clean_post_cache( $post );
		}
		*/
		
		// Flush LS Cache
		if ( class_exists('\LiteSpeed\Purge', false) ) {
			\LiteSpeed\Purge::purge_all();
			return true;
		} else if (has_action('litespeed_purge_all')) {
			do_action( 'litespeed_purge_all' );
			return true;
		} else if (function_exists('liteSpeed_purge_all')) {
			litespeed_purge_all();
			return true;
		}

		// W3 Total Cache
		if (function_exists('w3tc_flush_all')) {
			w3tc_flush_all();
			return true;
		} else if( $w3_plugin_totalcache ) {
			$w3_plugin_totalcache->flush_all();
			return true;
		}

		// WP Fastest Cache
		if (function_exists('wpfc_clear_all_cache')) {
			wpfc_clear_all_cache(true);
			return true;
		}

		// WP Rocket
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
			return true;
		}

		// WP Super Cache
		if(function_exists( 'prune_super_cache' ) && function_exists( 'get_supercache_dir' )) {
			prune_super_cache( get_supercache_dir(), true );
			return true;
		}

		// Cache Enabler
		if (function_exists( 'clear_site_cache' )) {
			clear_site_cache();
			return true;
		}

		// Comet Cache
		if(class_exists('comet_cache', false) && method_exists('comet_cache', 'clear')) {
			comet_cache::clear();
			return true;
		}
		
		// Clean Pagely cache
		if ( class_exists( 'PagelyCachePurge', false ) ) {
			(new PagelyCachePurge())->purgeAll();
			return true;
		}
		
		// Clean Hyper Cache
		if (function_exists('hyper_cache_clear')) {
			hyper_cache_clear();
			return true;
		}
			
		// Clean Simple Cache
		if (function_exists('simple_cache_flush')) {
			simple_cache_flush();
			return true;
		}
		
		// Clean Autoptimize
		if (class_exists('autoptimizeCache') && method_exists('autoptimizeCache', 'clearall')) {
			autoptimizeCache::clearall();
			return true;
		}
		
		// Clean WP-Optimize
		if (class_exists('WP_Optimize_Cache_Commands', false)) {
			( new WP_Optimize_Cache_Commands() )->purge_page_cache();
			return true;
		}
		
		return false;
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
	 * Check is cyrillic letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function is_cyr($string) {
		$pattern = '/[\x{0400}-\x{04FF}\x{0500}-\x{052F}\x{2DE0}-\x{2DFF}\x{A640}-\x{A69F}\x{1C80}-\x{1C8F}\x{0370}-\x{03FF}\x{1F00}-\x{1FFF}\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}\x{0530}-\x{058F}]+/u';
		return preg_match($pattern, strip_tags($string, '')) === 1;
	}

	/*
	 * Check is latin letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function is_lat($string){
		return !self::is_cyr($string);
	}
	
	/*
	 * Check is plugin active
	 */
	public static function is_plugin_active($plugin)
	{
		static $active_plugins = null;

		if ($active_plugins === null) {
			if (!function_exists('is_plugin_active')) {
				include_once WP_ADMIN_DIR . '/includes/plugin.php';
			}
			$active_plugins = get_option('active_plugins', []);
		}

		return in_array($plugin, $active_plugins);
	}
	
	/*
	 * Check if it is a block editor screen
	 * @since     1.0.9
	 * @version   1.0.0
	 */
	public static function is_editor() {
		// Static variable for caching the result
		static $is_editor = null;

		// If the result is already cached, return it
		if ($is_editor !== null) {
			return $is_editor;
		}

		// Check for specific editors and set the cache if true
		if (self::is_elementor_editor() || self::is_oxygen_editor()) {
			$is_editor = true;
			return $is_editor;
		}

		// Determine if the current screen is the block editor
		if (version_compare(get_bloginfo('version'), '5.0', '>=')) {
			if (!function_exists('get_current_screen')) {
				include_once WP_ADMIN_DIR . '/includes/screen.php';
			}
			$current_screen = get_current_screen();
			if (is_callable([$current_screen, 'is_block_editor']) && method_exists($current_screen, 'is_block_editor')) {
				$is_editor = $current_screen->is_block_editor();
			}
		} else {
			$is_editor = (isset($_GET['action']) && isset($_GET['post']) && $_GET['action'] == 'edit' && is_numeric($_GET['post']));
		}

		// Return the cached result
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
				return substr(str_rot13(bin2hex(random_bytes(ceil($length * 2))))??'', 0, $length);
			} else {
				return substr(str_rot13(bin2hex(openssl_random_pseudo_bytes(ceil($length * 2))))??'', 0, $length);
			}
		}
		else
		{
			return substr(str_replace(array('.',' ','_'),random_int(1000,9999),uniqid('t'.microtime()))??'', 0, $length);
		}
	}

	/*
	 * Delete all plugin translations
	 * @return        bool
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function clear_plugin_translations(){
		$domain_path = [
			path_join( WP_LANG_DIR, 'plugins' ) . '/serbian-transliteration-*.{po,mo,l10n.php}',
			dirname(RSTR_ROOT) . '/serbian-transliteration-*.{po,mo,l10n.php}',
			WP_LANG_DIR . '/serbian-transliteration-*.{po,mo,l10n.php}'
		];
		
		$i = 0;
		
		foreach ($domain_path as $pattern) {
			foreach (glob($pattern, GLOB_BRACE) as $file) {
				if (file_exists($file)) {
					@unlink($file);
					++$i;
				}
			}
		}
		
		return ($i > 0) ? true : false;
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
	
	/**
	 * Get current page ID
	 * @autor    Ivijan-Stefan Stipic
	 * @since    1.0.7
	 * @version  2.0.1
	 ******************************************************************/
	public static function get_page_ID() {
		global $post;

		// Static variable for caching the result
		static $current_page_id = null;

		// If the result is already cached, return it
		if ($current_page_id !== null) {
			return $current_page_id;
		}

		// Check different methods to get the page ID and cache the result
		if ($id = self::get_page_ID__private__wp_query()) {
			$current_page_id = $id;
		} else if ($id = self::get_page_ID__private__get_the_id()) {
			$current_page_id = $id;
		} else if (!is_null($post) && isset($post->ID) && !empty($post->ID)) {
			$current_page_id = $post->ID;
		} else if ($post = self::get_page_ID__private__GET_post()) {
			$current_page_id = $post;
		} else if ($p = self::get_page_ID__private__GET_p()) {
			$current_page_id = $p;
		} else if ($page_id = self::get_page_ID__private__GET_page_id()) {
			$current_page_id = $page_id;
		} else if ($id = self::get_page_ID__private__query()) {
			$current_page_id = $id;
		} else if ($id = self::get_page_ID__private__page_for_posts()) {
			$current_page_id = get_option('page_for_posts');
		} else {
			$current_page_id = false;
		}

		return $current_page_id;
	}

	// Get page ID by using get_the_id() function
	protected static function get_page_ID__private__get_the_id() {
		if (function_exists('get_the_id')) {
			if ($id = get_the_id()) return $id;
		}
		return false;
	}

	// Get page ID by wp_query
	protected static function get_page_ID__private__wp_query() {
		global $wp_query;
		return ((!is_null($wp_query) && isset($wp_query->post) && isset($wp_query->post->ID) && !empty($wp_query->post->ID)) ? $wp_query->post->ID : false);
	}

	// Get page ID by GET[post] in edit mode
	protected static function get_page_ID__private__GET_post() {
		return ((isset($_GET['action']) && sanitize_text_field($_GET['action']) == 'edit') && (isset($_GET['post']) && is_numeric($_GET['post'])) ? absint($_GET['post']) : false);
	}

	// Get page ID by GET[page_id]
	protected static function get_page_ID__private__GET_page_id() {
		return ((isset($_GET['page_id']) && is_numeric($_GET['page_id'])) ? absint($_GET['page_id']) : false);
	}

	// Get page ID by GET[p]
	protected static function get_page_ID__private__GET_p() {
		return ((isset($_GET['p']) && is_numeric($_GET['p'])) ? absint($_GET['p']) : false);
	}

	// Get page ID by OPTION[page_for_posts]
	protected static function get_page_ID__private__page_for_posts() {
		$page_for_posts = get_option('page_for_posts');
		return (!is_admin() && 'page' == get_option('show_on_front') && $page_for_posts ? absint($page_for_posts) : false);
	}

	// Get page ID by mySQL query
	protected static function get_page_ID__private__query() {
		if (is_admin()) {
			return false;
		}

		global $wpdb;
		$actual_link = rtrim($_SERVER['REQUEST_URI'], '/');
		
		// Parse the URL to get the path
		$parsed_url = parse_url($actual_link);
		$path = $parsed_url['path'];

		// Explode the path into parts and get the last part
		$parts = explode('/', trim($path, '/'));
		$slug = end($parts);

		if (!empty($slug)) {
			if ($post_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `ID` 
					FROM `{$wpdb->posts}`
					WHERE `post_status` = 'publish'
					AND `post_name` = %s
					AND TRIM(`post_name`) <> ''
					LIMIT 1",
					sanitize_title($slug)
				)
			)) {
				return absint($post_id);
			}
		}

		return false;
	}
	/**
	 * END Get current page ID
	 *****************************************************************/

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
			
			'ა' => 'a', 'Ა' => 'A', 'ბ' => 'b', 'Ბ' => 'B', 'გ' => 'g', 'Გ' => 'G',
			'დ' => 'd', 'Დ' => 'D', 'ე' => 'e', 'Ე' => 'E', 'ვ' => 'v', 'Ვ' => 'V',
			'ზ' => 'z', 'Ზ' => 'Z', 'თ' => 'th', 'Თ' => 'Th', 'ი' => 'i', 'Ი' => 'I',
			'კ' => 'k', 'Კ' => 'K', 'ლ' => 'l', 'Ლ' => 'L', 'მ' => 'm', 'Მ' => 'M',
			'ნ' => 'n', 'Ნ' => 'N', 'ო' => 'o', 'Ო' => 'O', 'პ' => 'p', 'Პ' => 'P',
			'ჟ' => 'zh', 'Ჟ' => 'Zh', 'რ' => 'r', 'Რ' => 'R', 'ს' => 's', 'Ს' => 'S',
			'ტ' => 't', 'Ტ' => 'T', 'უ' => 'u', 'Უ' => 'U', 'ფ' => 'ph', 'Ფ' => 'Ph',
			'ქ' => 'q', 'Ქ' => 'Q', 'ღ' => 'gh', 'Ღ' => 'Gh', 'ყ' => 'qh', 'Ყ' => 'Qh',
			'შ' => 'sh', 'Შ' => 'Sh', 'ჩ' => 'ch', 'Ჩ' => 'Ch', 'ც' => 'ts', 'Ც' => 'Ts',
			'ძ' => 'dz', 'Ძ' => 'Dz', 'წ' => 'ts', 'Წ' => 'Ts', 'ჭ' => 'tch', 'Ჭ' => 'Tch',
			'ხ' => 'kh', 'Ხ' => 'Kh', 'ჯ' => 'j', 'Ჯ' => 'J', 'ჰ' => 'h', 'Ჰ' => 'H',

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
	
	/*
	 * Get skip words
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function get_skip_words( $needle = NULL ){
		$locale = self::get_locale();
		$transient_name = RSTR_NAME . "-skip-words-{$locale}";
		$cache = Transliteration_Cache_DB::get($transient_name);
		if(empty($cache))
		{
			$file_name=apply_filters(
				'rstr/init/libraries/file/skip-words',
				"{$locale}.skip.words.lib",
				$locale,
				$transient_name
			);
			$cache = self::parse_library($file_name);
			if(!empty($cache)) {
				Transliteration_Cache_DB::set(
					$transient_name,
					$cache,
					apply_filters('rstr/init/libraries/file/skip-words/transient', (DAY_IN_SECONDS*7))
				);
			}
		}

		if($needle && is_array($cache)) {
			return (in_array($needle, $cache, true) !== false ? $needle : false);
		}

		return $cache;
	}
	
	/*
	 * Get list of diacriticals
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function get_diacritical( $needle = NULL ){
		$locale = self::get_locale();
		$transient_name = RSTR_NAME . "-diacritical-words-{$locale}";
		$cache = Transliteration_Cache_DB::get($transient_name);
		if(empty($cache))
		{
			$file_name=apply_filters(
				'rstr/init/libraries/file/get_diacritical',
				"{$locale}.diacritical.words.lib",
				$locale,
				$transient_name
			);
			$cache = self::parse_library($file_name);
			if(!empty($cache)) {
				Transliteration_Cache_DB::set(
					$transient_name,
					$cache,
					apply_filters('rstr/init/libraries/file/get_diacritical/transient', (DAY_IN_SECONDS*7))
				);
			}
		}

		if($needle && is_array($cache)) {
			return (in_array($needle, $cache, true) !== false ? $needle : false);
		}

		return $cache;
	}
	
	/*
	 * Parse library
	 * @return        bool false, array or string on needle
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function parse_library($file_name, $needle = NULL) {

		$words = array();
		$words_file=apply_filters('rstr/init/libraries/file', RSTR_ROOT . '/libraries/' . $file_name);

		if(file_exists($words_file))
		{
				$contents = '';
				if($read_file_chunks = self::read_file_chunks($words_file))
				{
					foreach ($read_file_chunks as $chunk) {
						$contents.=$chunk;
					}
				}

				if(!empty($contents))
				{
					$words = self::explode("\n", $contents);
					$words = array_unique($words);
				} else return false;
		} else return false;

		if($needle) {
			return (in_array($needle, $words, true) !== false ? $needle : false);
		} else {
			return $words;
		}
	}
	
	/**
	 * Parse URL
	 * @since     1.2.2
	 * @version   1.0.0
	 */
	public static function parse_url() {
		static $cachedUrl = null;

		if ($cachedUrl === null) {
			$http = 'http' . (self::is_ssl() ? 's' : '');
			$domain = rtrim(preg_replace('%:/{3,}%i', '://', $http . '://' . $_SERVER['HTTP_HOST']), '/');
			$url = preg_replace('%:/{3,}%i', '://', $domain . '/' . (isset($_SERVER['REQUEST_URI']) ? ltrim($_SERVER['REQUEST_URI'], '/') : ''));

			$cachedUrl = [
				'method' => $http,
				'home_fold' => str_replace($domain, '', home_url()),
				'url' => $url,
				'domain' => $domain,
			];
		}

		return $cachedUrl;
	}
	
	/*
	 * CHECK IS SSL
	 * @return  true/false
	 */
	public static function is_ssl($url = false)
	{
		if ($url !== false && preg_match('/^(https|ftps):/i', $url) === 1) {
			return true;
		}
		
		static $ssl = null;

		if ($ssl === null) {
			$conditions = [
				is_admin() && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN,
				($_SERVER['HTTPS'] ?? '') === 'on',
				($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
				($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on',
				($_SERVER['SERVER_PORT'] ?? 0) == 443,
				($_SERVER['HTTP_X_FORWARDED_PORT'] ?? 0) == 443,
				($_SERVER['REQUEST_SCHEME'] ?? '') === 'https'
			];

			$ssl = in_array(true, $conditions, true);
		}

		return $ssl;
	}

} endif;