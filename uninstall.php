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
// Plugin name
if (!defined('RSTR_NAME')) define('RSTR_NAME', 'serbian-transliteration');

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
//-END