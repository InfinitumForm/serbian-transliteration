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
delete_option(RSTR_NAME);
delete_option(RSTR_NAME . '-ID');
delete_option(RSTR_NAME . '-activation');
delete_option(RSTR_NAME . '-deactivation');
// Delete terms
if(term_exists('lat', 'rstr-script'))
{
	wp_delete_term(get_term_by('slug','lat','rstr-script')->term_id, 'rstr-script');
}
if(term_exists('cyr', 'rstr-script'))
{
	wp_delete_term(get_term_by('slug','cyr','rstr-script')->term_id, 'rstr-script');
}
//-END