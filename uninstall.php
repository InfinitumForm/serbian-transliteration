<?php
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