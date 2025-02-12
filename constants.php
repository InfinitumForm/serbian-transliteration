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

// Normalize Latin String map
if (!defined('RSTR_NORMALIZE_LATIN_STRING_MAP')) {
	define('RSTR_NORMALIZE_LATIN_STRING_MAP', array(
		'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Ă'=>'A', 'Ā'=>'A', 'Ą'=>'A', 'Æ'=>'A', 'Ǽ'=>'A',
		'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'ă'=>'a', 'ā'=>'a', 'ą'=>'a', 'æ'=>'a', 'ǽ'=>'a',

		'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',

		'Ç'=>'C', 'Č'=>'C', 'Ć'=>'C', 'Ĉ'=>'C', 'Ċ'=>'C',
		'ç'=>'c', 'č'=>'c', 'ć'=>'c', 'ĉ'=>'c', 'ċ'=>'c',

		'Đ'=>'Dj', 'Ď'=>'D',
		'đ'=>'dj', 'ď'=>'d',

		'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ĕ'=>'E', 'Ē'=>'E', 'Ę'=>'E', 'Ė'=>'E',
		'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'ę'=>'e', 'ė'=>'e',

		'Ĝ'=>'G', 'Ğ'=>'G', 'Ġ'=>'G', 'Ģ'=>'G',
		'ĝ'=>'g', 'ğ'=>'g', 'ġ'=>'g', 'ģ'=>'g',

		'Ĥ'=>'H', 'Ħ'=>'H',
		'ĥ'=>'h', 'ħ'=>'h',

		'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'Ĩ'=>'I', 'Ī'=>'I', 'Ĭ'=>'I', 'Į'=>'I',
		'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'į'=>'i', 'ĩ'=>'i', 'ī'=>'i', 'ĭ'=>'i', 'ı'=>'i',

		'Ĵ'=>'J',
		'ĵ'=>'j',

		'Ķ'=>'K', 'Ƙ'=>'K',
		'ķ'=>'k', 'ĸ'=>'k',

		'Ĺ'=>'L', 'Ļ'=>'L', 'Ľ'=>'L', 'Ŀ'=>'L', 'Ł'=>'L',
		'ĺ'=>'l', 'ļ'=>'l', 'ľ'=>'l', 'ŀ'=>'l', 'ł'=>'l',

		'Ñ'=>'N', 'Ń'=>'N', 'Ň'=>'N', 'Ņ'=>'N', 'Ŋ'=>'N',
		'ñ'=>'n', 'ń'=>'n', 'ň'=>'n', 'ņ'=>'n', 'ŋ'=>'n', 'ŉ'=>'n',

		'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ō'=>'O', 'Ŏ'=>'O', 'Ő'=>'O', 'Œ'=>'O',
		'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ō'=>'o', 'ŏ'=>'o', 'ő'=>'o', 'œ'=>'o', 'ð'=>'o',

		'Ŕ'=>'R', 'Ř'=>'R',
		'ŕ'=>'r', 'ř'=>'r', 'ŗ'=>'r',

		'Š'=>'S', 'Ŝ'=>'S', 'Ś'=>'S', 'Ş'=>'S',
		'š'=>'s', 'ŝ'=>'s', 'ś'=>'s', 'ş'=>'s',

		'Ŧ'=>'T', 'Ţ'=>'T', 'Ť'=>'T',
		'ŧ'=>'t', 'ţ'=>'t', 'ť'=>'t',

		'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ũ'=>'U', 'Ū'=>'U', 'Ŭ'=>'U', 'Ů'=>'U', 'Ű'=>'U', 'Ų'=>'U',
		'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ũ'=>'u', 'ū'=>'u', 'ŭ'=>'u', 'ů'=>'u', 'ű'=>'u', 'ų'=>'u',

		'Ŵ'=>'W', 'Ẁ'=>'W', 'Ẃ'=>'W', 'Ẅ'=>'W',
		'ŵ'=>'w', 'ẁ'=>'w', 'ẃ'=>'w', 'ẅ'=>'w',

		'Ý'=>'Y', 'Ÿ'=>'Y', 'Ŷ'=>'Y',
		'ý'=>'y', 'ÿ'=>'y', 'ŷ'=>'y',

		'Ž'=>'Z', 'Ź'=>'Z', 'Ż'=>'Z',
		'ž'=>'z', 'ź'=>'z', 'ż'=>'z',
		
		'ა' => 'a', 'Ა' => 'A', 'ბ' => 'b', 'Ბ' => 'B', 'გ' => 'g', 'Გ' => 'G',
		'დ' => 'd', 'Დ' => 'D', 'ე' => 'e', 'Ე' => 'E', 'ვ' => 'v', 'Ვ' => 'V',
		'ზ' => 'z', 'Ზ' => 'Z', 'თ' => 'th', 'Თ' => 'Th', 'ი' => 'i', 'Ი' => 'I',
		'კ' => 'k', 'Კ' => 'K', 'ლ' => 'l', 'Ლ' => 'L', 'მ' => 'm', 'Მ' => 'M',
		'ნ' => 'n', 'Ნ' => 'N', 'ო' => 'o', 'Ო' => 'O', 'პ' => 'p', 'Პ' => 'P',
		'ჟ' => 'zh', 'Ჟ' => 'Zh', 'რ' => 'r', 'Რ' => 'R', 'ს' => 's', 'Ს' => 'S',
		'ტ' => 't', 'Ტ' => 'T', 'უ' => 'u', 'Უ' => 'U', 'ფ' => 'ph', 'Ფ' => 'Ph',
		'ქ' => 'q', 'Ქ' => 'Q', 'ღ' => 'gh', 'Ღ' => 'Gh', 'ყ' => 'qh', 'Ყ' => 'Qh',
		'შ' => 'sh', 'Შ' => 'Sh', 'ჩ' => 'ch', 'Ჩ' => 'Ch', 'ც' => 'ts', 'Ც' => 'Ts',
		'ძ' => 'dz', 'Ძ' => 'Dz', 'წ' => 'ts', 'Წ' => 'Ts', 'ჭ' => 'tch', 'Ჭ' => 'Tch',
		'ხ' => 'kh', 'Ხ' => 'Kh', 'ჯ' => 'j', 'Ჯ' => 'J', 'ჰ' => 'h', 'Ჰ' => 'H',

		'“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '•'=>'-', '…'=>'...', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>__(' degrees ', 'serbian-transliteration'),
		'¼'=>' 1/4 ', '½'=>' 1/2 ', '¾'=>' 3/4 ', '⅓'=>' 1/3 ', '⅔'=>' 2/3 ', '⅛'=>' 1/8 ', '⅜'=>' 3/8 ', '⅝'=>' 5/8 ', '⅞'=>' 7/8 ',
		'÷'=>__(' divided by ', 'serbian-transliteration'), '×'=>__(' times ', 'serbian-transliteration'), '±'=>__(' plus-minus ', 'serbian-transliteration'), '√'=>__(' square root ', 'serbian-transliteration'),
		'∞'=>__(' infinity ', 'serbian-transliteration'), '≈'=>__(' almost equal to ', 'serbian-transliteration'), '≠'=>__(' not equal to ', 'serbian-transliteration'), 
		'≡'=>__(' identical to ', 'serbian-transliteration'), '≤'=>__(' less than or equal to ', 'serbian-transliteration'), '≥'=>__(' greater than or equal to ', 'serbian-transliteration'),
		'←'=>__(' left ', 'serbian-transliteration'), '→'=>__(' right ', 'serbian-transliteration'), '↑'=>__(' up ', 'serbian-transliteration'), '↓'=>__(' down ', 'serbian-transliteration'),
		'↔'=>__(' left and right ', 'serbian-transliteration'), '↕'=>__(' up and down ', 'serbian-transliteration'), '℅'=>__(' care of ', 'serbian-transliteration'), 
		'℮' => __(' estimated ', 'serbian-transliteration'), 'Ω'=>__(' ohm ', 'serbian-transliteration'), '♀'=>__(' female ', 'serbian-transliteration'), '♂'=>__(' male ', 'serbian-transliteration'),
		'©'=>__(' Copyright ', 'serbian-transliteration'), '®'=>__(' Registered ', 'serbian-transliteration'), '™' =>__(' Trademark ', 'serbian-transliteration'),
	));
}