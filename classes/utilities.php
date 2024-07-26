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
	
	public static function skip_transliteration(){
		return (isset($_REQUEST['rstr_skip']) && in_array($_REQUEST['rstr_skip'], ['true', true, 1, '1', 'yes']) !== false);
	}
	
	/*
	 * Plugin mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function plugin_mode($mode=NULL){
		$modes = [
			'light'		=> __('Light mode (light on memory and performance)', 'serbian-transliteration'),
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
			} elseif (count($check) === 2 && $check[0]($check[1], false)) {
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
				$sep = DIRECTORY_SEPARATOR;
				include_once( WP_ADMIN_DIR . "{$sep}includes{$sep}plugin-install.php" );
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
	public static function setcookie ($val) {
		if( !headers_sent() ) {

			setcookie( 'rstr_script', $val, (time()+YEAR_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
			Transliteration_Cache::delete('get_current_script');
			if(function_exists('nocache_headers')) nocache_headers();
			return true;
		}

		return false;
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

} endif;