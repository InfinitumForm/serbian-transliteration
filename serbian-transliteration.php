<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Transliterator
 * Plugin URI:        https://wordpress.org/plugins/serbian-transliteration/
 * Description:       All in one Cyrillic to Latin transliteration plugin for WordPress that actually works.
 * Donate link:       https://www.buymeacoffee.com/ivijanstefan
 * Version:           1.12.5
 * Requires at least: 5.4
 * Tested up to:      6.4
 * Requires PHP:      7.0
 * Author:            Ivijan-Stefan Stipić
 * Author URI:        https://profiles.wordpress.org/ivijanstefan/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       serbian-transliteration
 * Domain Path:       /languages
 * Network:           true
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// If someone try to called this file directly via URL, abort.
if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Database version
if ( ! defined( 'RSTR_DATABASE_VERSION' ) ){
	define( 'RSTR_DATABASE_VERSION', '1.0.1');
}

/*
 * Main plugin constants
 * @since     1.1.0
 * @verson    1.0.0
 */
// Main plugin file
if ( ! defined( 'RSTR_FILE' ) ) define( 'RSTR_FILE', __FILE__ );
// Set of constants
include_once __DIR__ . '/constants.php';

/*
 * Serbian transliteration cache
 * @since     1.0.0
 * @verson    1.0.0
 */
include_once RSTR_INC . '/Cache.php';
if ( defined( 'RSTR_DEBUG_CACHE' ) && RSTR_DEBUG_CACHE === true ) {
	add_action('wp_footer', function(){
		if(is_user_logged_in() && current_user_can('administrator')) {
			Serbian_Transliteration_Cache::debug();
		}
	});
}

/*
 * Get plugin options
 * @since     1.1.3
 * @verson    1.0.0
 */
if(!function_exists('get_rstr_option'))
{
	function get_rstr_option($name = false, $default = NULL) {
		static $get_rstr_options = null;

		if ($get_rstr_options === null) {
			$get_rstr_options = Serbian_Transliteration_Cache::get('options');
			if (!$get_rstr_options) {
				$get_rstr_options = Serbian_Transliteration_Cache::set('options', get_option(RSTR_NAME));
			}
		}

		if ($name === false) {
			return $get_rstr_options ?: $default;
		}

		return isset($get_rstr_options[$name]) ? $get_rstr_options[$name] : $default;
	}
}

/*
 * Serbian transliteration requirements
 * @since     1.0.0
 * @verson    1.0.0
 */
include_once RSTR_INC . '/Requirements.php';
$Serbian_Transliteration_Activate = new Serbian_Transliteration_Requirements(array('file' => RSTR_FILE));

if($Serbian_Transliteration_Activate->passes()) :
	/*
	 * Register database tables
	 * @since     1.8.1
	 * @verson    1.0.0
	 */
	global $wpdb;
	$wpdb->rstr_cache = $wpdb->get_blog_prefix() . 'rstr_cache';
	
	/*
	 * Serbian transliteration database cache
	 * @since     1.5.7
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Cache_DB.php';
	Serbian_Transliteration_DB_Cache::instance();
	
	$includes = [
		'/Cache_DB.php',          // Serbian transliteration database cache
		'/Utilities.php',         // Serbian transliteration utilities
		'/Transliteration.php',   // Serbian transliteration requirements
		'/Global.php',            // Main global classes with active hooks
		'/Functions.php',         // Include functions
		'/SEO.php',               // Include SEO
		'/Tools.php',             // Include Tools
		'/Plugins.php',           // Include Plugins Support
		'/Themes.php',            // Include Themes Support
		'/WP_CLI.php',            // WP-CLI
		'/Notice.php',            // Notice
		'/Init.php'               // Initialize active plugin
	];

	foreach ($includes as $file) {
		include_once RSTR_INC . $file;
	} unset($includes);

	if( class_exists('Serbian_Transliteration_Init') ) :
		/* Do translations
		====================================*/
		add_action('plugins_loaded', function () {
			if (is_textdomain_loaded(RSTR_NAME)) {
				return;
			}

			if (!function_exists('is_user_logged_in')) {
				include_once ABSPATH . '/wp-includes/pluggable.php';
			}

			$locale = apply_filters('rstr_plugin_locale', get_user_locale(), 'serbian-transliteration');
			$mofile = sprintf('%s-%s.mo', 'serbian-transliteration', $locale);

			// Prvo proveravamo prevode unutar direktorijuma plugina
			$domain_path = __DIR__ . DIRECTORY_SEPARATOR . 'languages';
			$loaded = load_textdomain(RSTR_NAME, path_join($domain_path, $mofile));

			// Ako prevod nije pronađen, proveravamo globalni direktorijum
			if (!$loaded) {
				$domain_path = path_join(WP_LANG_DIR, 'plugins');
				$loaded = load_textdomain(RSTR_NAME, path_join($domain_path, $mofile));
			}

			// Ako ni to ne uspe, proveravamo direktno u WP_LANG_DIR
			if (!$loaded) {
				$loaded = load_textdomain(RSTR_NAME, path_join(WP_LANG_DIR, $mofile));
			}
		});


		/* Activate plugin
		====================================*/
		Serbian_Transliteration::register_activation_hook(function(){
			if (!current_user_can('activate_plugins')) {
				return;
			}

			Serbian_Transliteration_Utilities::attachment_taxonomies();
			
			// Save version and set activation date
			update_option(RSTR_NAME . '-version', RSTR_VERSION, false);

			$activation = get_option(RSTR_NAME . '-activation', []);
			$activation[] = date('Y-m-d H:i:s');
			update_option(RSTR_NAME . '-activation', $activation);

			// Generate unique ID
			if (!get_option(RSTR_NAME . '-ID')) {
				add_option(RSTR_NAME . '-ID', Serbian_Transliteration_Utilities::generate_token(64));
			}

			// Set default options if not set
			$options = get_option(RSTR_NAME, Serbian_Transliteration_Utilities::plugin_default_options());
			$options = array_merge(Serbian_Transliteration_Utilities::plugin_default_options(), $options);
			add_option(RSTR_NAME, $options);

			// Set important cookie
			$firstVisitMode = get_rstr_option('first-visit-mode');
			$transliterationMode = get_rstr_option('transliteration-mode');

			if (!isset($_COOKIE['rstr_script'])) {
				if (in_array($firstVisitMode, ['lat', 'cyr'])) {
					Serbian_Transliteration_Utilities::setcookie($firstVisitMode);
				} else {
					$mode = $transliterationMode === 'cyr_to_lat' ? 'lat' : 'cyr';
					Serbian_Transliteration_Utilities::setcookie($mode);
				}
			}

			Serbian_Transliteration_Utilities::clear_plugin_cache();
			Serbian_Transliteration_Utilities::clear_plugin_translations();

			// Add custom script languages
			foreach (['lat' => 'Latin', 'cyr' => 'Cyrillic'] as $slug => $name) {
				if (!term_exists($slug, 'rstr-script')) {
					wp_insert_term($name, 'rstr-script', array('slug' => $slug));
				}
			}

			// Assign terms to the settings
			$termScript = get_option(RSTR_NAME . '-term-script', []);
			foreach (['lat', 'cyr'] as $slug) {
				if (!isset($termScript[$slug])) {
					$termScript[$slug] = get_term_by('slug', $slug, 'rstr-script')->term_id;
				}
			}
			add_option(RSTR_NAME . '-term-script', $termScript);

			// Install database tables
			if (RSTR_DATABASE_VERSION !== get_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION)) {
				Serbian_Transliteration_DB_Cache::table_install();
				update_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION, false);
			}

			flush_rewrite_rules();

			return true;
		});

		
		/* Redirect after activation
		====================================*/
		add_action('init', function () {
			add_action('activated_plugin', function ($plugin) {
				if( $plugin === RSTR_BASENAME && !get_option(RSTR_NAME.'-activated', false)) {
					set_option(RSTR_NAME.'-activated', true);
					if( wp_safe_redirect( admin_url( 'options-general.php?page=serbian-transliteration&rstr-activation=true' ) ) ) {
						exit;
					}
				}
			}, 10, 1);
		});


		/* Run script on the plugin upgrade
		====================================*/
		add_action( 'plugins_loaded', function () {
			if( is_admin() && (RSTR_VERSION !== get_option(RSTR_NAME . '-version', RSTR_VERSION)) ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}
				
				// Delete old translations
				Serbian_Transliteration_Utilities::clear_plugin_translations();
				
				// Install database tables
				if( RSTR_DATABASE_VERSION !== get_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION) ) {
					Serbian_Transliteration_DB_Cache::table_install();
					update_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION, false);
				}
				
				// Clear plugin cache
				Serbian_Transliteration_Utilities::clear_plugin_cache();
				
				// Unload textdomain
				unload_textdomain(RSTR_NAME);

				// Reset permalinks
				flush_rewrite_rules();
				
				// Save version
				update_option(RSTR_NAME . '-version', RSTR_VERSION, false);
			}
		}, 1 );
		

		/* Deactivate plugin
		====================================*/
		Serbian_Transliteration::register_deactivation_hook(function(){
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			
			// Delete old translations
			Serbian_Transliteration_Utilities::clear_plugin_translations();

			// Add deactivation date
			if($deactivation = get_option(RSTR_NAME . '-deactivation')) {
				$deactivation[] = date('Y-m-d H:i:s');
				update_option(RSTR_NAME . '-deactivation', $deactivation);
			} else {
				add_option(RSTR_NAME . '-deactivation', array(date('Y-m-d H:i:s')));
			}

			Serbian_Transliteration_Utilities::clear_plugin_cache();
			
			// Unload textdomain
			unload_textdomain(RSTR_NAME);

			// Reset permalinks
			flush_rewrite_rules();
		});
		
		/* Clear cache on the post update
		====================================*/
		add_action('transition_post_status', function(){
			// Clear plugin cache
			Serbian_Transliteration_Utilities::clear_plugin_cache();
			Serbian_Transliteration_DB_Cache::flush();
		});

		/* Load tools
		====================================*/
		Serbian_Transliteration_Tools::instance();
		
		/* Run plugin
		====================================*/
		Serbian_Transliteration_Init::run(); // Run in frontend
		
		Serbian_Transliteration_Init::run_dependency(); // Run dependency
	endif;
endif;