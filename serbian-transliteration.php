<?php
/**
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 * @wordpress-plugin
 * Plugin Name:       Transliterator - WordPress Transliteration
 * Plugin URI:        https://wordpress.org/plugins/serbian-transliteration/
 * Description:       All in one Cyrillic to Latin transliteration plugin for WordPress that actually works.
 * Version:           1.6.4
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

// Globals
global $rstr_cache;

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
$rstr_cache = new Serbian_Transliteration_Cache();

if ( defined( 'RSTR_DEBUG_CACHE' ) && RSTR_DEBUG_CACHE === true ) {
	add_action('wp_footer', function(){
		if(is_user_logged_in() && current_user_can('administrator')) {
			global $rstr_cache;
			$rstr_cache->debug();
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
		global $rstr_cache;
		$get_rstr_options = $rstr_cache->get('options');

		if( !$get_rstr_options ){
			$get_rstr_options = $rstr_cache->set('options', get_option( RSTR_NAME ));
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
	 * Serbian transliteration utilities
	 * @since     1.5.7
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Utilities.php';
	
	/*
	 * Serbian transliteration statistic
	 * @since     1.5.7
	 * @verson    1.0.0
	 */
	include_once RSTR_INC . '/Statistic.php';

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
	$Serbian_Transliteration_Tools = new Serbian_Transliteration_Tools();
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
	if(class_exists('Serbian_Transliteration_Init')) :
		/* Do translations
		====================================*/
		add_action('plugins_loaded', function () {
			$locale = apply_filters( 'rstr_plugin_locale', get_locale(), RSTR_NAME );
			if ( $loaded = load_textdomain( RSTR_NAME, RSTR_ROOT . '/languages' . '/' . RSTR_NAME . '-' . $locale . '.mo' ) ) {
				return $loaded;
			} else {
				load_plugin_textdomain( RSTR_NAME, FALSE, RSTR_ROOT . '/languages' );
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

			// Reset permalinks
			flush_rewrite_rules();
			
			// Send plugin statistic
			Serbian_Transliteration_Statistic::activation(array('settings' => $options));

			return $success;
		});

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
			
			// Send plugin statistic
			Serbian_Transliteration_Statistic::deactivation();
		});

		/* Run plugin
		====================================*/
		// Run in frontend
		add_action('init', array('Serbian_Transliteration_Init', 'run'));
		// Run dependency
		Serbian_Transliteration_Init::run_dependency();
	endif;
endif;
