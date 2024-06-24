<?php
/**
 * Uninstall plugin and clean everything
 *
 * @link              http://infinitumform.com/
 * @package           Serbian_Transliteration
 */
 
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

global $wpdb;

$RSTR_NAME = 'serbian-transliteration';

delete_option($RSTR_NAME);
delete_option($RSTR_NAME . '-ID');
delete_option($RSTR_NAME . '-activation');
delete_option($RSTR_NAME . '-deactivation');
delete_option($RSTR_NAME . '-term-script');
delete_option($RSTR_NAME . '-html-tags');
delete_option($RSTR_NAME . '-reviewed');
delete_option($RSTR_NAME . '-version');
delete_option($RSTR_NAME . '-db-version');
delete_option($RSTR_NAME . '-activated');
delete_option($RSTR_NAME . '-db-cache-table-exists');

if (term_exists('lat', 'rstr-script')) {
	wp_delete_term(get_term_by('slug', 'lat', 'rstr-script')->term_id, 'rstr-script');
}
if (term_exists('cyr', 'rstr-script')) {
	wp_delete_term(get_term_by('slug', 'cyr', 'rstr-script')->term_id, 'rstr-script');
}

if (!headers_sent()) {
	setcookie('rstr_script', '', (time() - YEAR_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
}

delete_transient($RSTR_NAME . '-skip-words');
delete_transient($RSTR_NAME . '-diacritical-words');
delete_transient($RSTR_NAME . '-locales');

if ($wpdb) {
	$wpdb->query("DELETE FROM `{$wpdb->options}` WHERE `{$wpdb->options}`.`option_name` REGEXP '^_transient_(.*)?{$RSTR_NAME}(.*|$)'");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rstr_cache");
}

foreach ([
	path_join(WP_LANG_DIR, 'plugins') . '/' . $RSTR_NAME . '-*.{po,mo}',
	WP_LANG_DIR . '/' . $RSTR_NAME . '-*.{po,mo}'
] as $pattern) {
	foreach (glob($pattern, GLOB_BRACE) as $file) {
		if (file_exists($file)) {
			unlink($file);
		}
	}
}
//-END