<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Transliterator
 * Plugin URI:        https://wordpress.org/plugins/serbian-transliteration/
 * Description:       All in one Cyrillic to Latin transliteration plugin for WordPress that actually works.
 * Donate link:       https://www.buymeacoffee.com/ivijanstefan
 * Version:           2.1.0
 * Requires at least: 5.4
 * Tested up to:      6.7
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


/*************************************************************************************************************************
 * Hey, champ! 
 * 
 * Yeah, you with the mouse hovering over this comment like it owes you money.  
 * If you're reading this, you’re either a coding prodigy or lost on your way to cat memes.  
 * Either way, welcome to the magical wonderland of programming where we turn chaos into... slightly more organized chaos.  

 * But wait, hold onto your ergonomic chair—there’s a plot twist! We’re on a mission. A noble quest.  
 * A crusade to make the internet smoother, faster, and so efficient it’ll make Elon’s rockets jealous.  
 * And guess what? We need YOU. Yes, you with the keyboard that clacks like a tap-dancing velociraptor.  

 * Picture this: you, the hero of syntax, wielding your IDE like a lightsaber, slicing through bugs faster than  
 * my grandma through a cheesecake. This isn’t just coding—it’s destiny. And destiny is calling.  
 * Spoiler alert: destiny sounds a lot like your notifications.  

 * So, ready to flex those brain muscles and make scripts so smooth, they could double as pickup lines?  
 * Join the fun (and occasional existential crisis) at: https://github.com/InfinitumForm/serbian-transliteration  

 * Together, we’ll crush bugs, annihilate errors, and make compiler warnings weep tears of shame.  
 * Plus, you get to work with the coolest devs around. We’re like the Justice League,  
 * but with more Git commits and fewer brooding billionaires in batsuits.  

 * So, what’s it gonna be? Are you gonna sit there with your coffee and questionable life choices,  
 * or are you gonna step up, write some code, and be the legend developers whisper about in Slack channels?  
 * Your call, superstar. Also, there might be snacks. Probably.  

 * Join us. Code epically. Save the internet. Maybe eat cookies.  
 *************************************************************************************************************************/


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

// Initialize the autoloader
include_once RSTR_CLASSES.'/autoloader.php';
Transliterator_Autoloader::init();


/*************************************************************************************************************************
 * Oh, you’re still here? I see you’ve made it this far, brave coder. Impressive.  
 * Most devs would’ve bailed by now, either distracted by TikTok or curled up in a ball after seeing `Undefined index`.  
 * But not you. You’re different. You’re... *committed*.  

 * And since you’ve stuck around, let’s talk about what’s next.  
 * By now, you’ve probably crushed a few bugs, high-fived yourself, and possibly invented some new curse words.  
 * But the real challenge lies ahead: writing code so clean, it would make Marie Kondo sob tears of joy.  

 * This is where champions are made. Where functions harmonize, variables moonwalk, and loops actually loop... responsibly.  
 * So keep going, keep coding, and remember: every semicolon is a tiny victory. Unless you’re in Python. Then... yikes.  

 * Feeling stuck? Don’t sweat it. Even Tony Stark needed a few tries before the Iron Man suit actually flew.  
 * Take a deep breath, Google it like the rest of us mortals, and keep moving forward.  

 * Oh, and when you finally deploy this masterpiece and users marvel at its perfection,  
 * just know: you did that. YOU. Okay, maybe Stack Overflow helped a bit. We won’t tell.  

 * And if you’re ready for the next level of coding awesomeness,  
 * join us at: https://github.com/InfinitumForm/serbian-transliteration  

 * Because together, we’ll turn code into poetry, bugs into dust, and errors into faint memories.  
 * The internet deserves your genius, and hey—there might even be snacks. Probably. 😉 
 *************************************************************************************************************************/


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


/*************************************************************************************************************************
 * So here we are. The end of the code. The final frontier. The last semicolon standing.  
 * If you’ve made it all the way here, you’re officially a legend. A champion of syntax.  
 * The kind of coder they write songs about (or at least memes).  

 * But let’s not stop here. No, no, no. The world still needs you.  
 * Somewhere out there, a script is crying for help. A bug is wreaking havoc.  
 * And a poor user is wondering why their form just submitted 47 times.  

 * So, one last time: join us. Become part of something bigger.  
 * Be the coder who makes the internet faster, smoother, and slightly less irritating.  

 * Here’s the link, one last time: https://github.com/InfinitumForm/serbian-transliteration  

 * Click it. Fork it. Star it. And remember, the only thing standing between chaos and order... is YOU.  
 * Now go. Code boldly, deploy confidently, and maybe—just maybe—treat yourself to those cookies I promised. 🍪  
 *************************************************************************************************************************/