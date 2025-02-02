<?php if ( !defined('WPINC') ) die();

// Find wp-admin file path
if (!defined('WP_ADMIN_DIR')) {
    // Default wp-admin directory
    $wp_admin_dir = ABSPATH . 'wp-admin';

    // Verify if wp-admin exists
    if (!is_dir($wp_admin_dir)) {
        // Fallback if wp-admin is not found in the default location
        $wp_admin_dir = dirname(WP_CONTENT_DIR) . '/wp-admin';
    }

    // Define the constant WP_ADMIN_DIR
    define('WP_ADMIN_DIR', rtrim($wp_admin_dir, '/\\'));
}

// Include Dependency
$include_dependency = false;
if (!function_exists('is_plugin_active_for_network') || !function_exists('is_plugin_active')) {
    if (file_exists(WP_ADMIN_DIR . '/includes/plugin.php')) {
        include_once WP_ADMIN_DIR . '/includes/plugin.php';
        $include_dependency = true;
    }
}

/*
 * Main plugin constants
 * @since     1.0.0
 * @verson    1.0.0
*/

// This is need for plugin debugging.
if (defined('WP_DEBUG')) {
    if (WP_DEBUG === true || WP_DEBUG === 1) {
        if (!defined('RSTR_DEBUG')) define('RSTR_DEBUG', true);
    }
}

// Plugin basename
if (!defined('RSTR_BASENAME')) {
    define('RSTR_BASENAME', plugin_basename(RSTR_FILE));
}
// Plugin root
if (!defined('RSTR_ROOT')) {
    define('RSTR_ROOT', rtrim(plugin_dir_path(RSTR_FILE) , '/'));
}
// Plugin URL root
if (!defined('RSTR_URL')) {
    define('RSTR_URL', rtrim(plugin_dir_url(RSTR_FILE) , '/'));
}
// Assets URL
if (!defined('RSTR_ASSETS')) {
    define('RSTR_ASSETS', RSTR_URL . '/assets');
}
// Classes
if (!defined('RSTR_CLASSES')) {
    define('RSTR_CLASSES', RSTR_ROOT . '/classes');
}
// Plugin name
if (!defined('RSTR_NAME')) {
    define('RSTR_NAME', 'serbian-transliteration');
}
// Plugin table
if (!defined('RSTR_TABLE')) {
    define('RSTR_TABLE', 'serbian_transliteration');
}

// Current plugin version ( if change, clear also session cache )
if (function_exists('get_file_data') && $plugin_data = get_file_data(RSTR_FILE, array(
    'Version' => 'Version'
) , false)) {
    $rstr_version = $plugin_data['Version'];
}

if (!$rstr_version && preg_match('/\*[\s\t]+?version:[\s\t]+?([0-9.]+)/i', file_get_contents(RSTR_FILE) , $v)) {
    $rstr_version = $v[1];
}

if (!defined('RSTR_VERSION')) {
    define('RSTR_VERSION', $rstr_version);
}
// Plugin session prefix (controlled by version)
if (!defined('RSTR_PREFIX')) {
    define('RSTR_PREFIX', RSTR_TABLE . '_' . preg_replace("~[^0-9]~Ui", '', RSTR_VERSION) . '_');
}
// Is multisite
if (!defined('RSTR_MULTISITE')) {
    define('RSTR_MULTISITE', function_exists('is_plugin_active_for_network') ? is_plugin_active_for_network(RSTR_BASENAME) : false);
}
if (!defined('RSTR_MULTISITE')) {
    define('RSTR_MULTISITE', false);
}

// Is Woocommerce exists
if (!defined('RSTR_WOOCOMMERCE')) {
    define('RSTR_WOOCOMMERCE', (function_exists('is_plugin_active') ? is_plugin_active('woocommerce/woocommerce.php') : false));
}