<?php if ( !defined('WPINC') ) die();

class Transliteration_Utilities {
	use Transliteration__Cache;
	
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
			'mode'							=> 'advanced',
			'avoid-admin'					=> 'no',
			'allow-admin-tools'				=> 'yes',
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
	public static function skip_transliteration() {
		return self::cached_static('skip_transliteration', function(){
			return (isset($_REQUEST['rstr_skip']) && in_array(is_string($_REQUEST['rstr_skip']) ? strtolower($_REQUEST['rstr_skip']) : $_REQUEST['rstr_skip'], ['true', true, 1, '1', 'yes'], true) !== false);
		});
	}
	
	/*
	 * Retrieve available plugin modes.
	 * Returns an array of mode keys with support for WooCommerce and developer-specific settings.
	 *
	 * @param  string|null $mode Optional specific mode key to check.
	 * @return array|string      Array of mode keys or specific mode key if exists.
	 */
	public static function available_modes($mode = NULL) {
		// Define available mode keys
		$modes = [
			'light',
			'standard',
			'advanced',
			'forced',
		];

		// Add WooCommerce-specific mode key if WooCommerce is enabled
		if (RSTR_WOOCOMMERCE) {
			$modes[] = 'woocommerce';
		}

		// Add developer mode key if in development environment
		if (defined('RSTR_DEV_MODE') && RSTR_DEV_MODE) {
			$modes[] = 'dev';
		}

		// Allow filtering of mode keys
		$modes = apply_filters('rstr_plugin_mode_key', $modes);

		// Return specific mode key if $mode is provided
		if ($mode) {
			return in_array($mode, $modes, true) ? $mode : [];
		}

		return $modes;
	}

	/*
	 * Retrieve plugin modes with descriptions.
	 * Modes include predefined options with support for WooCommerce and developer-specific settings.
	 *
	 * @param  string|null $mode Optional specific mode key to retrieve.
	 * @return array|string      Modes array with labels or specific mode description.
	 */
	public static function plugin_mode($mode = NULL) {
		// Get available modes
		$available_modes = self::available_modes();

		// Map available modes to their descriptions
		$modes = [
			'light'     => __('Light mode (basic parts of the site)', 'serbian-transliteration'),
			'standard'  => __('Standard mode (content, themes, plugins, translations, menu)', 'serbian-transliteration'),
			'advanced'  => __('Advanced mode (content, widgets, themes, plugins, translations, menu, permalinks, media)', 'serbian-transliteration'),
			'forced'    => __('Forced transliteration (everything)', 'serbian-transliteration'),
			'woocommerce' => __('Only WooCommerce (It bypasses all other transliterations and focuses only on WooCommerce)', 'serbian-transliteration'),
			'dev'       => __('Dev Mode (Only for developers and testers)', 'serbian-transliteration'),
		];

		// Filter the modes to include only available modes
		$filtered_modes = array_intersect_key($modes, array_flip($available_modes));

		// Allow filtering of the labeled modes
		$filtered_modes = apply_filters('rstr_plugin_mode', $filtered_modes);

		// Return specific mode description if $mode is provided
		if ($mode) {
			return $filtered_modes[$mode] ?? [];
		}

		return $filtered_modes;
	}
	
	/*
	 * Get locale
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function get_locale($locale = NULL) {
		return self::cached_static('get_locale', function(){
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
		});
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
		return self::cached_static('exclude_transliteration', function(){
			$locale = self::get_locale();
			$exclude = get_rstr_option('disable-by-language', []);

			return isset($exclude[$locale]) && $exclude[$locale] === 'yes';
		});		
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
		
		if( !is_string($content) && is_numeric($content) ) {
			return false;
		}
		
		$content = self::decode($content);
		
		$urlRegex = '/^((https?|s?ftp):)?\/\/([a-zA-Z0-9\-\._\+]+(:[a-zA-Z0-9\-\._]+)?@)?([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,})(:[0-9]+)?(\/[a-zA-Z0-9\-\._\%\&=\?\+\$\[\]\(\)\*\'\,\.\,\:]+)*\/?#?$/i';
		
		$emailRegex = '/^[a-zA-Z0-9\-\._\p{L}]+@[a-zA-Z0-9\-\._\p{L}]+\.[a-zA-Z]{2,}$/i';

		return !empty($content) && is_string($content) && strlen($content) > 10 && (preg_match($urlRegex, $content) || preg_match($emailRegex, $content));
	}

	
	/*
	 * Has cache plugin active
	 * @version    1.0.0
	 */
	public static function has_cache_plugin() {
		return self::cached_static('has_cache_plugin', function(){
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
				
				$count = count($check);
				
				if ($count === 1 && $check[0]) {
					return true;
					break;
				} elseif ($count === 2 && in_array($check[0], ['function_exists', 'has_action']) && $check[0]($check[1])) {
					return true;
					break;
				} elseif ($count === 2 && 'class_exists' === $check[0] && $check[0]($check[1], false)) {
					return true;
					break;
				} elseif ($count === 3 && $check[0]($check[1]) && $check[0]($check[2])) {
					return true;
					break;
				} elseif ($count === 4 && $check[0]($check[1], false) && $check[2]($check[3])) {
					return true;
					break;
				}
			}

			return false;
		});
	}
	
	/*
	 * Return plugin informations
	 * @return        array/object
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function plugin_info($fields = array()) {
		return self::cached_static('plugin_info', function() use ($fields) {
			if ( is_admin() ) {
				
				if ( ! function_exists( 'plugins_api' ) ) {
					include_once WP_ADMIN_DIR . '/includes/plugin-install.php';
				}
				/** Prepare our query */
				//donate_link
				//versions
				return plugins_api( 'plugin_information', array(
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
			}
			
			return false;
		}, $fields);
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
			if (headers_sent()) {
				return false;
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
		return self::cached_static('get_current_script', function() {
			// Cookie mode
			if( !empty( $_COOKIE['rstr_script'] ?? NULL ) ){
				switch( sanitize_text_field($_COOKIE['rstr_script']) ) {
					case 'lat':
						return 'lat';
						break;
					
					case 'cyr':
						return 'cyr';
						break;
				}
			}
			
			// Set new script
			return get_rstr_option('first-visit-mode', 'lat');
		});
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
	public static function decode($content, $flag = ENT_NOQUOTES) {
		if (!empty($content) && is_string($content) && !is_numeric($content) && !is_array($content) && !is_object($content)) {
			if (filter_var($content, FILTER_VALIDATE_URL) && strpos($content, '%') !== false) {
				// If content is a valid URL and contains encoded characters
				$content = rawurldecode($content);
			} else {
				// Decode HTML entities
				$content = html_entity_decode($content, $flag);
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
		return self::cached_static('already_cyrillic', function() {
			return in_array(self::get_locale(), apply_filters('rstr_already_cyrillic', array('sr_RS','mk_MK', 'bel', 'bg_BG', 'ru_RU', 'sah', 'uk', 'kk', 'el', 'ar', 'hy'))) !== false;
		});
	}
	
	/*
	 * Check is cyrillic letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function is_cyr($string) {
		if( !is_string($string) || is_numeric($string) ) {
			return false;
		}
		
		$string = strip_tags($string, '');
		
		if (strlen($string) > 10) {
			$string = substr($string, 0, 10);
		}
	
		$pattern = '/[\x{0400}-\x{04FF}\x{0500}-\x{052F}\x{2DE0}-\x{2DFF}\x{A640}-\x{A69F}\x{1C80}-\x{1C8F}\x{0370}-\x{03FF}\x{1F00}-\x{1FFF}\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}\x{0530}-\x{058F}]+/u';
		
		return preg_match($pattern, $string) === 1;
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
		return self::cached_static('is_editor', function() {
			$is_editor = false;

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
			}
			
			if( !$is_editor ) {
				$is_editor = ( isset($_GET['action'], $_GET['post']) && $_GET['action'] === 'edit' && is_numeric($_GET['post']) );
			}

			// Return the cached result
			return $is_editor;
		});
	}
	
	/* 
	 * Check is in the Elementor editor mode
	 * @verson    1.0.0
	 */
	public static function is_elementor_editor(){
		return self::cached_static('is_elementor_editor', function() {
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
		});
	}
	
	/* 
	 * Check is in the Elementor preview mode
	 * @verson    1.0.0
	 */
	public static function is_elementor_preview(){
		return self::cached_static('is_elementor_preview', function() {
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
		});
	}
	
	/* 
	 * Check is in the Oxygen editor mode
	 * @verson    1.0.0
	 */
	public static function is_oxygen_editor(){
		return self::cached_static('is_oxygen_editor', function() {
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
		});
	}

	/*
	 * Generate unique token
	 * @author    Ivijan-Stefan Stipic
	 */
	public static function generate_token($length = 16) {
		if (function_exists('random_bytes')) {
			// Koristimo random_bytes za generisanje kriptografski sigurnog tokena
			$bytes = random_bytes(ceil($length * 2));
		} elseif (function_exists('openssl_random_pseudo_bytes')) {
			// Fallback na openssl_random_pseudo_bytes ako random_bytes nije dostupan
			$bytes = openssl_random_pseudo_bytes(ceil($length * 2));
		} else {
			// Fallback na uniqid ako nijedna od gore navedenih funkcija nije dostupna
			$bytes = str_replace(array('.', ' ', '_'), random_int(1000, 9999), uniqid('t'.microtime()));
		}

		// Vraćanje tokena koji je rotiran i skraćen na željenu dužinu
		return substr(str_rot13(bin2hex($bytes)), 0, $length);
	}

	/*
	 * Delete all plugin translations
	 * @return        bool
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function clear_plugin_translations() {
		$domain_paths = [
			path_join(WP_LANG_DIR, 'plugins') . '/serbian-transliteration-*.{po,mo,l10n.php}',
			dirname(RSTR_ROOT) . '/serbian-transliteration-*.{po,mo,l10n.php}',
			WP_LANG_DIR . '/serbian-transliteration-*.{po,mo,l10n.php}'
		];
		
		$deleted_files = 0;

		foreach ($domain_paths as $pattern) {
			foreach (glob($pattern, GLOB_BRACE) as $file) {
				if (@unlink($file)) {
					$deleted_files++;
				} else {
					error_log(sprintf(__('Failed to delete plugin translation: %s', 'serbian-transliteration'), $file));
				}
			}
		}

		return $deleted_files > 0;
	}
	
	/*
	* PHP Wrapper for explode — Split a string by a string
	* @since     1.0.9
	* @verson    1.0.0
	* @url       https://www.php.net/manual/en/function.explode.php
	*/
	public static function explode(string $separator, string $string, ?int $limit = PHP_INT_MAX, bool $keep_empty = false) {
		if ($separator === '') {
			// Ako je separator prazan, vratite prazan niz ili prijavite grešku
			return [];
		}
		
		// Handle limit
		if($limit === NULL) {
			$limit = PHP_INT_MAX;
		}

		// Explode string
		$string = explode($separator, ($string ?? ''), $limit);

		// Trim whitespace from each element
		$string = array_map('trim', $string);

		// Optionally filter out empty elements
		if (!$keep_empty) {
			$string = array_filter($string, function($value) {
				return $value !== '';
			});
		}

		return $string;
	}
	
	/**
	 * Get current page ID
	 * @autor    Ivijan-Stefan Stipic
	 * @since    1.0.7
	 * @version  2.0.1
	 ******************************************************************/
	public static function get_page_ID() {
		return self::cached_static('get_page_ID', function() {
			global $post;
			// Check different methods to get the page ID and cache the result
			if ($id = self::get_page_ID__private__wp_query()) {
				return $id;
			} else if ($id = self::get_page_ID__private__get_the_id()) {
				return $id;
			} else if (!is_null($post) && isset($post->ID) && !empty($post->ID)) {
				return $post->ID;
			} else if ($post = self::get_page_ID__private__GET_post()) {
				return $post;
			} else if ($p = self::get_page_ID__private__GET_p()) {
				return $p;
			} else if ($page_id = self::get_page_ID__private__GET_page_id()) {
				return $page_id;
			} else if ($id = self::get_page_ID__private__query()) {
				return $id;
			} else if ($id = self::get_page_ID__private__page_for_posts()) {
				return get_option('page_for_posts');
			}
			
			return false;
		});
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
		$actual_link = rtrim($_SERVER['REQUEST_URI']??'', '/');
		
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

		$str = strtr($str, $map);
		
		if(function_exists('remove_accents')) {
			$str = remove_accents($str);
		}
		
		return $str;
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
			$domain = rtrim(preg_replace('%:/{3,}%i', '://', $http . '://' . ($_SERVER['HTTP_HOST']??'localhost')), '/');
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
		return self::cached_static('is_ssl', function() use ($url) {
			if ($url !== false && preg_match('/^(https|ftps):/i', $url) === 1) {
				return true;
			}
			
			$conditions = [
				is_admin() && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN,
				($_SERVER['HTTPS'] ?? '') === 'on',
				($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
				($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on',
				($_SERVER['SERVER_PORT'] ?? 0) == 443,
				($_SERVER['HTTP_X_FORWARDED_PORT'] ?? 0) == 443,
				($_SERVER['REQUEST_SCHEME'] ?? '') === 'https'
			];

			return in_array(true, $conditions, true);
		}, $url);
	}
	
	public static function array_filter($array, $remove, $reindex = false) {
		
		if( empty($remove) ) {
			if ($reindex && array_values($array) === $array) {
				return array_values($array);
			}

			return $array;
		}
		
		if( !is_array($remove) ) {
			$remove = self::explode(',', $remove);
		}
		
		$array = array_filter($array, function($value) use ($remove) {
			if (is_array($value)) {
				$value = self::array_filter($value, $remove);
			}

			foreach ($remove as $item) {
				if (is_array($value) && is_array($item)) {
					// Dubinsko poređenje nizova
					if ($value == $item) {
						return false;
					}
				} elseif ($value === $item) {
					return false;
				}
			}

			return true;
		});

		if ($reindex && array_values($array) === $array) {
			return array_values($array);
		}

		return $array;
	}
	
	/*
	 * Check if it's wp-admin
	 * @return  true/false
	 */
	public static function is_admin() {
		return self::cached_static('is_admin', function() {
			global $rstr_is_admin;
			return $rstr_is_admin || (is_admin() && !wp_doing_ajax());
		});
	}


}