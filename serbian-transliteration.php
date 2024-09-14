<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Transliterator
 * Plugin URI:        https://wordpress.org/plugins/serbian-transliteration/
 * Description:       All in one Cyrillic to Latin transliteration plugin for WordPress that actually works.
 * Donate link:       https://www.buymeacoffee.com/ivijanstefan
 * Version:           2.0.5
 * Requires at least: 5.4
 * Tested up to:      6.6
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
if ( ! defined( 'WPINC' ) ) {
	die( "Don't mess with us." );
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Database version
if ( ! defined( 'RSTR_DATABASE_VERSION' ) ){
	define( 'RSTR_DATABASE_VERSION', '1.0.1');
}

/**
 * Main plugin constants
 * @since     1.1.0
 * @verson    1.0.0
 */
// Main plugin file
if ( ! defined( 'RSTR_FILE' ) ) define( 'RSTR_FILE', __FILE__ );

// Required constants
if( !defined('COOKIEHASH') || !defined('COOKIEPATH') || !defined('COOKIE_DOMAIN') ) {
	if( !function_exists('wp_cookie_constants') ) {
		include_once ABSPATH.WPINC.'/default-constants.php';
	}
	
	if( function_exists('wp_cookie_constants') ) {
		wp_cookie_constants();
	}
}

// Set of constants
include_once __DIR__ . '/constants.php';

// Developers need good debug
if( (defined('RSTR_DEV_MODE') && RSTR_DEV_MODE) || (defined('RSTR_DEBUG') && RSTR_DEBUG) ) {
	error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
	add_action('doing_it_wrong_run', '__return_false');
	ini_set('display_errors', true);
	ini_set('log_errors', true);
}

// Set database tables
global $wpdb, $rstr_is_admin;
$wpdb->rstr_cache = $wpdb->get_blog_prefix() . 'rstr_cache';

// Check is in admin mode
$rstr_is_admin = ($_COOKIE['rstr_test_' . COOKIEHASH]??'false'==='true');

/*
 * Get plugin options
 * @since     1.1.3
 * @verson    1.0.0
 */
if(!function_exists('get_rstr_option'))
{
	function get_rstr_option($name = false, $default = NULL) {
		static $get_rstr_options = NULL;

		if ($get_rstr_options === NULL) {
			if (!$get_rstr_options) {
				$get_rstr_options = get_option('serbian-transliteration');
			}
		}

		if ($name === false) {
			return $get_rstr_options ?: $default;
		}

		return isset($get_rstr_options[$name]) ? $get_rstr_options[$name] : $default;
	}
}

// Register the autoload function
spl_autoload_register(function ($class_name) {
    // Define the prefix to directory mapping
    $prefixes = [
        'Transliteration_Map_'    => RSTR_CLASSES . '/maps/',
        'Transliteration_Mode_'   => RSTR_CLASSES . '/modes/',
        'Transliteration_Plugin_' => RSTR_CLASSES . '/plugins/',
        'Transliteration_Theme_'  => RSTR_CLASSES . '/themes/',
        'Transliteration_'        => RSTR_CLASSES . '/'
    ];
    
    // Static cache array to store resolved class paths
    static $class_map_cache = [];

    // Check if the class is already cached
    if (isset($class_map_cache[$class_name])) {
        if (!class_exists($class_name, false)) {
            require_once $class_map_cache[$class_name];
        }
        return;
    }

    // Iterate over the prefix mappings
    foreach ($prefixes as $prefix => $directory) {
        // Check if the class name starts with the prefix and if the class does not already exist
        if (strpos($class_name, $prefix) === 0 && !class_exists($class_name, false)) {
            // Remove the prefix from the class name
            $class_file = str_replace($prefix, '', $class_name);
            
            // Handle different naming conventions
            if ($prefix == 'Transliteration_Map_') {
                // For Transliteration_Map_, retain underscores
                $class_file = str_replace('-', '_', $class_file);
            } else {
                // For other prefixes, convert underscores to hyphens and lowercase the file name
                $class_file = strtolower(str_replace('_', '-', $class_file));
            }
            
            // Define the file path
            $file = $directory . $class_file . '.php';
            
            // Check if the file exists and is not an index file, then cache and require it
            if (strpos($file, 'index.php') === false && file_exists($file)) {
                $class_map_cache[$class_name] = $file;
                require_once $file;
                return;
            }
        }
    }
});


// Transliteration requirements
$transliteration_requirements = new Transliteration_Requirements(array('file' => RSTR_FILE));

// Plugin is ready for the run
if($transliteration_requirements->passes()) :
	// Ensure the main model class is loaded first
	require_once RSTR_CLASSES . '/model.php';
	
	// Ensure the WP_CLI class is loaaded second
	require_once RSTR_CLASSES . '/wp-cli.php';

	// On the plugin activation
	register_activation_hook(RSTR_FILE, ['Transliteration_Init', 'register_activation']);

	// On the deactivation
	register_deactivation_hook(RSTR_FILE, ['Transliteration_Init', 'register_deactivation']);

	// On the plugin update
	add_action('upgrader_process_complete', ['Transliteration_Init', 'register_updater'], 10, 2);
	
	// On the manual plugin update
	add_action('admin_init', ['Transliteration_Init', 'check_plugin_update']);

	// Redirect after activation
	add_action('admin_init', ['Transliteration_Init', 'register_redirection'], 10, 2);

	// Run the plugin
	Transliteration::run_the_plugin();

	// Plugin Functions
	include_once __DIR__ . '/functions.php';
endif;

// Clear memory
unset($transliteration_requirements);

/**
 * Hey you! Yeah, you with the impeccable taste in code and a knack for solving problems.
 * If you're reading this, it means you're about to dive into the magical world of programming.
 * But wait, there's more! How about joining our crusade to make the internet a better place for everyone
 * who needs smooth and efficient script conversion? You know you want to. 😉
 *
 * Picture this: You, a keyboard warrior, typing away, turning one script into another faster than a caffeinated squirrel
 * on a sugar high. It’s not just coding, it’s a heroic quest! And let’s face it, who doesn’t want to be a hero?
 *
 * We need your superpowers at: https://github.com/InfinitumForm/serbian-transliteration
 *
 * Join us, and together we'll vanquish the evil bugs, slay the nasty errors, and laugh in the face of compiler warnings.
 * Plus, you'll get to work with some of the coolest developers this side of the internet. Seriously, our team is like
 * the Avengers, but with more coffee and fewer capes.
 *
 * So what are you waiting for? Don’t be the developer who just watches from the sidelines. Be the legend who writes
 * code so glorious, it’ll be sung about in future developer meetups. Also, there might be cookies. Maybe.
 *
 * Embrace your inner hero and join our quest. Coding has never been this epic.
 */