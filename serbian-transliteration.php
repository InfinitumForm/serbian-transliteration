<?php
/**
 * @wordpress-plugin
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 * @wordpress-plugin
 * Plugin Name:       Transliterator
 * Plugin URI:        https://wordpress.org/plugins/serbian-transliteration/
 * Description:       All in one Cyrillic to Latin transliteration plugin for WordPress that actually works.
 * Donate link:       https://www.buymeacoffee.com/ivijanstefan
 * Version:           1.9.11
 * Requires at least: 5.4
 * Tested up to:      6.3
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
		$get_rstr_options = Serbian_Transliteration_Cache::get('options');

		if( !$get_rstr_options ){
			$get_rstr_options = Serbian_Transliteration_Cache::set('options', get_option( RSTR_NAME ));
		}

		if($get_rstr_options) {
			if( $name === false ){
				return $get_rstr_options;
			} else {
				if(isset($get_rstr_options[$name])) {
					return !empty($get_rstr_options[$name]) ? $get_rstr_options[$name] : $default;
				}
			}
		}

		return $default;
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
	
	/*
	 * Serbian transliteration utilities
	 * @since     1.5.7
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Utilities.php';

	/*
	 * Serbian transliteration requirements
	 * @since     1.0.0
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Transliteration.php';

	/*
	 * Main global classes with active hooks
	 * @since     1.1.0
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Global.php';

	/*
	 * Include functions
	 * @since     1.0.9
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Functions.php';

	/*
	 * Include SEO
	 * @since     1.3.5
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/SEO.php';

	/*
	 * Include Tools
	 * @since     1.1.0
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Tools.php';
	/*
	 * Include Plugins Support
	 * @since     1.2.3
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Plugins.php';
	/*
	 * Include Themes Support
	 * @since     1.2.3
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Themes.php';
	/*
	 * WP-CLI
	 * @since     1.4.3
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/WP_CLI.php';
	/*
	 * WP-CLI
	 * @since     1.4.3
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Notice.php';
	/*
	 * Initialize active plugin
	 * @since     1.0.0
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Init.php';
	if( class_exists('Serbian_Transliteration_Init') ) :
		/* Do translations
		====================================*/
		add_action('plugins_loaded', function () {
			if ( is_textdomain_loaded( RSTR_NAME ) ) {
				return;
			}
			
			if(!function_exists('is_user_logged_in')){
				include_once ABSPATH  . '/wp-includes/pluggable.php';
			}
		
			$locale = get_locale();
			if( is_user_logged_in() ) {
				if( $user_locale = get_user_locale( get_current_user_id() ) ) {
					$locale = $user_locale;
				}
			}
			$locale = apply_filters( 'rstr_plugin_locale', $locale, RSTR_NAME );
			
			$mofile = sprintf( '%s-%s.mo', RSTR_NAME, $locale );
			// Check first inside `/wp-content/languages/plugins`
			$domain_path = path_join( WP_LANG_DIR, 'plugins' );
			$loaded = load_textdomain( RSTR_NAME, path_join( $domain_path, $mofile ) );
			// Or inside `/wp-content/languages`
			if ( ! $loaded ) {
				$loaded = load_textdomain( RSTR_NAME, path_join( WP_LANG_DIR, $mofile ) );
			}
			// Or inside `/wp-content/plugin/cf-geoplugin/languages`
			if ( ! $loaded ) {
				$domain_path = __DIR__ . DIRECTORY_SEPARATOR . 'languages';
				$loaded = load_textdomain( RSTR_NAME, path_join( $domain_path, $mofile ) );
				// Or load with only locale without prefix
				if ( ! $loaded ) {
					$loaded = load_textdomain( RSTR_NAME, path_join( $domain_path, "{$locale}.mo" ) );
				}
				// Or old fashion way
				if ( ! $loaded && function_exists('load_plugin_textdomain') ) {
					load_plugin_textdomain( RSTR_NAME, false, $domain_path );
				}
			}
		});

		/* Activate plugin
		====================================*/
		Serbian_Transliteration::register_activation_hook(function(){
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$success = true;

			Serbian_Transliteration_Utilities::attachment_taxonomies();
			
			// Save version
			update_option(RSTR_NAME . '-version', RSTR_VERSION, false);

			// Add activation date
			if($activation = get_option(RSTR_NAME . '-activation')) {
				$activation[] = date('Y-m-d H:i:s');
				update_option(RSTR_NAME . '-activation', $activation);
			} else {
				add_option(RSTR_NAME . '-activation', array(date('Y-m-d H:i:s')));
			}

			// Generate unique ID
			if(!get_option(RSTR_NAME . '-ID')) {
				add_option(RSTR_NAME . '-ID', Serbian_Transliteration_Utilities::generate_token(64));
			}

			// Get current options
			$options = get_option(RSTR_NAME);

			if(empty($options))
			{
				// Set default pharams
				$options = Serbian_Transliteration_Utilities::plugin_default_options();
				add_option( RSTR_NAME, $options );
			}
			else
			{
				// Set missing options
				$added=0;
				foreach(Serbian_Transliteration_Utilities::plugin_default_options() as $key => $value){
					if( !(isset($options[$key])) ){
						$options[$key] = $value;
						++$added;
					}
				}
				// Clear variables
				$key = $value = NULL;
				// Save new data
				if( $added > 0 ) {
					add_option( RSTR_NAME, $options );
				}
			}

			// Set important cookie
			if( !(isset($_COOKIE['rstr_script'])) )
			{
				if(get_rstr_option('first-visit-mode') == 'lat') {
					Serbian_Transliteration_Utilities::setcookie('lat');
				} else if(get_rstr_option('first-visit-mode') == 'cyr') {
					Serbian_Transliteration_Utilities::setcookie('cyr');
				} else {
					if(get_rstr_option('transliteration-mode') == 'cyr_to_lat') {
						Serbian_Transliteration_Utilities::setcookie('lat');
					} else if(get_rstr_option('transliteration-mode') == 'lat_to_cyr') {
						Serbian_Transliteration_Utilities::setcookie('cyr');
					}
				}
			}

			// Clear plugin cache
			Serbian_Transliteration_Utilities::clear_plugin_cache();

			// Add custom script languages
			if(!term_exists('lat', 'rstr-script'))
			{
				wp_insert_term('Latin', 'rstr-script', array('slug'=>'lat'));
			}
			if(!term_exists('cyr', 'rstr-script'))
			{
				wp_insert_term('Cyrillic', 'rstr-script', array('slug'=>'cyr'));
			}

			// Assign terms to the settings
			if(!get_option(RSTR_NAME . '-term-script'))
			{
				add_option(RSTR_NAME . '-term-script', array(
					'lat' => get_term_by('slug', 'lat', 'rstr-script')->term_id,
					'cyr' => get_term_by('slug', 'cyr', 'rstr-script')->term_id
				));
			}
			
			// Install database tables
			if( RSTR_DATABASE_VERSION !== get_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION) ) {
				Serbian_Transliteration_DB_Cache::table_install();
				update_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION, false);
			}

			// Reset permalinks
			flush_rewrite_rules();

			return $success;
		});
		
		/* Redirect after activation
		====================================*/
/*
		add_action('activated_plugin', function ($plugin) {
			if( $plugin === RSTR_BASENAME ) {
				if( wp_safe_redirect( admin_url( 'options-general.php?page=serbian-transliteration&rstr-activation=true' ) ) ) {
					exit;
				}
			}
		}, 10, 1);
*/

		/* Run script on the plugin upgrade
		 ====================================*/
		add_action( 'plugins_loaded', function () {
			if( is_admin() && (RSTR_VERSION !== get_option(RSTR_NAME . '-version', RSTR_VERSION)) ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}
				
				// Install database tables
				if( RSTR_DATABASE_VERSION !== get_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION) ) {
					Serbian_Transliteration_DB_Cache::table_install();
					update_option(RSTR_NAME . '-db-version', RSTR_DATABASE_VERSION, false);
				}
				
				// Clear plugin cache
				Serbian_Transliteration_Utilities::clear_plugin_cache();

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

			// Add deactivation date
			if($deactivation = get_option(RSTR_NAME . '-deactivation')) {
				$deactivation[] = date('Y-m-d H:i:s');
				update_option(RSTR_NAME . '-deactivation', $deactivation);
			} else {
				add_option(RSTR_NAME . '-deactivation', array(date('Y-m-d H:i:s')));
			}

			Serbian_Transliteration_Utilities::clear_plugin_cache();

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
		// Run in frontend
		add_action('init', array('Serbian_Transliteration_Init', 'run'));
		// Run dependency
		Serbian_Transliteration_Init::run_dependency();
	endif;
endif;
