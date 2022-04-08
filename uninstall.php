<?php
/**
 * Uninstall plugin and clean everything
 *
 * @link              http://infinitumform.com/
 * @package           Serbian_Transliteration
 */
 
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Plugin name
if (!defined('RSTR_NAME')) define('RSTR_NAME', 'serbian-transliteration');
$RSTR_NAME = RSTR_NAME;

// Remove statistic data
if(file_exists(__DIR__ . '/inc/Statistic.php')) {
	include_once __DIR__ . '/inc/Statistic.php';
	Serbian_Transliteration_Statistic::uninstall();
}

// Delete options
if(get_option(RSTR_NAME)) {
	delete_option(RSTR_NAME);
}
if(get_option(RSTR_NAME . '-ID')) {
	delete_option(RSTR_NAME . '-ID');
}
if(get_option(RSTR_NAME. '-activation')) {
	delete_option(RSTR_NAME . '-activation');
}
if(get_option(RSTR_NAME . '-deactivation')) {
	delete_option(RSTR_NAME . '-deactivation');
}
if(get_option(RSTR_NAME . '-term-script')) {
	delete_option(RSTR_NAME . '-term-script');
}
if(get_option(RSTR_NAME . '-html-tags')) {
	delete_option(RSTR_NAME . '-html-tags');
}
if(get_option(RSTR_NAME . '-reviewed')) {
	delete_option(RSTR_NAME . '-reviewed');
}
if(get_option(RSTR_NAME . '-version')) {
	delete_option(RSTR_NAME . '-version');
}
// Delete terms
if(term_exists('lat', 'rstr-script')) {
	wp_delete_term(get_term_by('slug','lat','rstr-script')->term_id, 'rstr-script');
}
if(term_exists('cyr', 'rstr-script')) {
	wp_delete_term(get_term_by('slug','cyr','rstr-script')->term_id, 'rstr-script');
}
// Delete cookie
if( !headers_sent() ) {
	setcookie( 'rstr_script', '', (time()-YEAR_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
}
// Delete transients
if(get_transient(RSTR_NAME . '-skip-words')) {
	delete_transient(RSTR_NAME . '-skip-words');
}
if(get_transient(RSTR_NAME . '-diacritical-words')) {
	delete_transient(RSTR_NAME . '-diacritical-words');
}
if(get_transient(RSTR_NAME . '-locales')) {
	delete_transient(RSTR_NAME . '-locales');
}

if($wpdb) {
	$wpdb->query("DELETE FROM `{$wpdb->options}` WHERE `{$wpdb->options}`.`option_name` REGEXP '^_transient_(.*)?{$RSTR_NAME}(.*|$)'");
}
//-END