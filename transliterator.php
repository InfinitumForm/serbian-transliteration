<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Transliterator
 * Plugin URI:        https://wordpress.org/plugins/serbian-transliteration/
 * Description:       All in one Cyrillic to Latin transliteration plugin for WordPress that actually works.
 * Donate link:       https://www.buymeacoffee.com/ivijanstefan
 * Version:           2.0.0
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

// Set of constants
include_once __DIR__ . '/constants.php';

// Set database tables
global $wpdb;
$wpdb->rstr_cache = $wpdb->get_blog_prefix() . 'rstr_cache';

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
        'Transliteration_Map_' => RSTR_CLASSES . '/maps/',
		'Transliteration_Mode_' => RSTR_CLASSES . '/modes/',
        'Transliteration_'      => RSTR_CLASSES . '/'
    ];
    
    // Iterate over the prefix mappings
    foreach ($prefixes as $prefix => $directory) {
        // Check if the class name starts with the prefix and if the class does not already exist
        if (strpos($class_name, $prefix) === 0 && !class_exists($class_name, false)) {
            // Remove the prefix from the class name and convert underscores to hyphens
            $class_file = str_replace([$prefix, '_'], ['', '-'], $class_name);
            // Define the file path
			$class_file = ( $prefix == 'Transliteration_Map_' ? str_replace(['-'], ['_'], $class_file) : strtolower($class_file) );
            $file = $directory . $class_file . '.php';
			
            // Check if the file exists and require it
            if (strpos($file, '/index.php') === false && file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Ensure the main model class is loaded first
require_once RSTR_CLASSES . '/model.php';

// Run the plugin
Transliteration::run_the_plugin();