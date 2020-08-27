<?php
/**
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 *
 * @wordpress-plugin
 * Plugin Name:       Serbian Transliteration
 * Plugin URI:        http://infinitumform.com/
 * Description:       The only Serbian transliteration plugin for WordPress that actually works.
 * Version:           1.0.0
 * Author:            INFINITUM FORM
 * Author URI:        https://infinitumform.com/
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
if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Find is localhost or not
if ( ! defined( 'RSTR_LOCAL' ) ) {
	if(isset($_SERVER['REMOTE_ADDR'])) {
		define('RSTR_LOCAL', in_array($_SERVER['REMOTE_ADDR'], array(
			'127.0.0.1',
			'::1'
		)));
	} else {
		define('RSTR_LOCAL', false);
	}
}

/**
 * DEBUG MODE
 *
 * This is need for plugin debugging.
 */
if ( defined( 'WP_DEBUG' ) ){
	if(WP_DEBUG === true || WP_DEBUG === 1)
	{
		if ( ! defined( 'RSTR_DEBUG' ) ) define( 'RSTR_DEBUG', true );
	}
}
if ( defined( 'RSTR_DEBUG' ) ){
	if(RSTR_DEBUG === true || RSTR_DEBUG === 1)
	{
		error_reporting( E_ALL );
		if(function_exists('ini_set'))
		{
			ini_set('display_startup_errors',1);
			ini_set('display_errors',1);
		}
	}
}

// Global class
global $RSTR_USERS, $RSTR_USERS_ARRAY;

// Find wp-admin file path
if ( strrpos(WP_CONTENT_DIR, '/wp-content/', 1) !== false) {
    $WP_ADMIN_DIR = substr(WP_CONTENT_DIR, 0, -10) . 'wp-admin';
} else {
    $WP_ADMIN_DIR = substr(WP_CONTENT_DIR, 0, -11) . '/wp-admin';
}
if (!defined('WP_ADMIN_DIR')) define('WP_ADMIN_DIR', $WP_ADMIN_DIR);
// Main plugin file
if ( ! defined( 'RSTR_FILE' ) )			define( 'RSTR_FILE', __FILE__ );
// Plugin root
if ( ! defined( 'RSTR_ROOT' ) )			define( 'RSTR_ROOT', rtrim(plugin_dir_path(RSTR_FILE), '/') );
// Plugin URL root
if ( ! defined( 'RSTR_URL' ) )			define( 'RSTR_URL', rtrim(plugin_dir_url( RSTR_FILE ), '/') );
// Assets URL
if ( ! defined( 'RSTR_ASSETS' ) )		define( 'RSTR_ASSETS', RSTR_URL.'/assets' );
// Classes
if ( ! defined( 'RSTR_INC' ) )			define( 'RSTR_INC', RSTR_ROOT.'/inc' );
// Plugin name
if ( ! defined( 'RSTR_NAME' ) )			define( 'RSTR_NAME', 'serbian-transliteration');
// Plugin table
if ( ! defined( 'RSTR_TABLE' ) )		define( 'RSTR_TABLE', 'serbian_transliteration');
// Plugin metabox prefix
if ( ! defined( 'RSTR_METABOX' ) )		define( 'RSTR_METABOX', RSTR_TABLE . '_metabox_');
// Current plugin version ( if change, clear also session cache )
$RSTR_version = NULL;
if(function_exists('get_file_data') && $plugin_data = get_file_data( RSTR_FILE, array('Version' => 'Version'), false ))
	$RSTR_version = $plugin_data['Version'];
if(!$RSTR_version && preg_match('/\*[\s\t]+?version:[\s\t]+?([0-9.]+)/i', file_get_contents( RSTR_FILE ), $v))
	$RSTR_version = $v[1];
if ( ! defined( 'RSTR_VERSION' ) )			define( 'RSTR_VERSION', $RSTR_version);
// Plugin session prefix (controlled by version)
if ( ! defined( 'RSTR_PREFIX' ) )		define( 'RSTR_PREFIX', RSTR_TABLE . '_' . preg_replace("/[^0-9]/Ui", '', RSTR_VERSION) . '_');

/*
 * Main global classes with active hooks
 * @since     1.0.0
 * @verson    1.0.0
 */
if(!class_exists('Serbian_Transliteration')) :
class Serbian_Transliteration{
	
	private $get_locale;
	
	public $lat = array(
		// Big letters
		'A', 'B', 'V', 'G', 'D', 'Đ', 'E', 'Ž', 'Z', 'I', 'J', 'K', 'L', 'LJ', 'M',
		'N', 'NJ', 'O', 'P', 'R', 'S', 'T', 'Ć', 'U', 'F', 'H', 'C', 'Č', 'DŽ', 'Š',
		// Small letters
		'a', 'b', 'v', 'g', 'd', 'đ', 'e', 'ž', 'z', 'i', 'j', 'k', 'l', 'lj', 'm',
		'n', 'nj', 'o', 'p', 'r', 's', 't', 'ć', 'u', 'f', 'h', 'c', 'č', 'dž', 'š',
		// Variations
		'Nj', 'Lj', 'Dž', 'Dj', 'DJ', 'dj', 'dz', 'JU', 'ju', 'JA', 'ja' ,'ŠČ' ,'šč'
	);
	
	public $cyr = array(
		// Big letters
		'А', 'Б', 'В', 'Г', 'Д', 'Ђ', 'Е', 'Ж', 'З', 'И', 'Ј', 'К', 'Л', 'Љ', 'М',
		'Н', 'Њ', 'О', 'П', 'Р', 'С', 'Т', 'Ћ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш',
		// Small letters
		'а', 'б', 'в', 'г', 'д', 'ђ', 'е', 'ж', 'з', 'и', 'ј', 'к', 'л', 'љ', 'м',
		'н', 'њ', 'о', 'п', 'р', 'с', 'т', 'ћ', 'у', 'ф', 'х', 'ц', 'ч', 'џ', 'ш',
		// Variations
		'Њ', 'Љ', 'Џ', 'Ђ', 'Ђ', 'ђ', 'ѕ', 'Ю', 'ю', 'Я', 'я' ,'Щ' ,'щ'
	);
	
	public function already_cyrillic(){
        return in_array($this->get_locale(), array('sr_RS','bs_BA')) !== false;
	}
	
	public function get_locale(){
		if(!$this->get_locale){
			$this->get_locale = get_locale();
		}
        return $this->get_locale;
	}
	
	public function fix_html($content){
		
		$tags = explode(',', '!DOCTYPE,a,abbr,acronym,address,applet,area,article,aside,audio,b,base,basefont,bdi,bdo,big,blockquote,body,br,button,canvas,caption,center,cite,code,col,colgroup,data,details,dd,del,details,dfn,dialog,dir,div,dl,dt,em,embed,fieldset,figcaption,figure,font,footer,form,frame,frameset,h1,h2,h3,h4,h5,h6,head,header,hr,html,i,iframe,img,input,ins,kbd,label,legend,li,link,main,map,mark,meta,master,nav,noframes,noscript,object,ol,optgroup,option,output,p,param,picture,pre,progress,q,rp,rt,ruby,s,samp,script,section,select,small,source,span,strike,strong,style,sub,summary,sup,svg,table,tbody,td,template,textarea,tfoot,th,thead,time,title,tr,track,tt,u,ul,var,video,wbr');
		$tags = array_map('trim', $tags);
		$tags = array_filter($tags);
		
		$tags_cyr = $tags_lat = array();
		foreach($tags as $tag){
			$tags_cyr[]='<' . str_replace($this->lat, $this->cyr, $tag);
			$tags_cyr[]='</' . str_replace($this->lat, $this->cyr, $tag) . '>';
			
			$tags_lat[]= '<' . $tag;
			$tags_lat[]= '</' . $tag . '>';
		}
		
		$tags_cyr = array_merge($tags_cyr, array('&нбсп;','&лт;','&гт;','&ндасх;','&мдасх;','хреф','срц','&лдqуо;','&бдqуо;','&лсqуо;','&рсqуо;','&сцарон;','&Сцарон;','&тилде;'));
		$tags_lat = array_merge($tags_lat, array('&nbsp;','&lt;','&gt;','&ndash;','&mdash;','href','src','&ldquo;','&bdquo;','&lsquo;','&rsquo;','ш','Ш','&tilde;'));
		
		$content = str_replace($tags_cyr, $tags_lat, $content);
		
		$lastPos = 0;
		$positions = [];

		while (($lastPos = mb_strpos($content, '<', $lastPos, 'UTF-8')) !== false) {
			$positions[] = $lastPos;
			$lastPos = $lastPos + mb_strlen('<', 'UTF-8');
		}

		foreach ($positions as $position) {
			if(mb_strpos($content, '>', 0, 'UTF-8') !== false) {
				$end   = mb_strpos($content, ">", $position, 'UTF-8') - $position;
				$tag  = mb_substr($content, $position, $end, 'UTF-8');
				$tag_lat = str_replace($this->cyr, $this->lat, $tag);
				$content = str_replace($tag, $tag_lat, $content);
			}
		}
		
		$content = preg_replace_callback ('/\&([\x{0400}-\x{04FF}0-9]+)\;/iu', function($m){
			return '&' . str_replace($this->cyr, $this->lat, $m[1]) . ';';
		}, $content);
		
		return $content;
	}
	
	/*
	 * Plugin mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function plugin_mode($mode=NULL){
		$modes = apply_filters('serbian_transliteration_plugin_mode', array(
			'standard' => __('Standard mode (content, themes, plugins, translations, menu)', RSTR_NAME),
			'advanced' => __('Advanced mode (content, widgets, themes, plugins, translations, menu‚ permalinks, media)', RSTR_NAME),
			'forced' => __('Forced transliteration (everything - it may cause the problems)', RSTR_NAME)
		));
		
		if($mode && isset($modes[$mode])){
			return $modes[$mode];
		}
		
		return $modes;
	}
	
	/*
	 * Transliteration mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function transliteration_mode($mode=NULL){
		$modes = apply_filters('serbian_transliteration_transliteration_mode', array(
			'none' => __('Transliteration disabled', RSTR_NAME),
			'cyr_to_lat' => __('Cyrillic to Latin', RSTR_NAME),
			'lat_to_cyr' => __('Latin to Cyrillic', RSTR_NAME)
		));
		
		if($mode && isset($modes[$mode])){
			return $modes[$mode];
		}
		
		return $modes;
	}
	
	/*
	 * Hook for register_activation_hook()
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function register_uninstall_hook($function){
		return register_uninstall_hook( RSTR_FILE, $function );
	}
	
	/*
	 * Hook for register_deactivation_hook()
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function register_deactivation_hook($function){
		return register_deactivation_hook( RSTR_FILE, $function );
	}
	
	/*
	 * Hook for register_activation_hook()
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function register_activation_hook($function){
		return register_activation_hook( RSTR_FILE, $function );
	}
	/* 
	 * Hook for add_action()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1){
		if(!is_array($function_to_add))
			$function_to_add = array(&$this, $function_to_add);
			
		return add_action( (string)$tag, $function_to_add, (int)$priority, (int)$accepted_args );
	}
	
	/* 
	 * Hook for add_filter()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1){
		if(!is_array($function_to_add))
			$function_to_add = array(&$this, $function_to_add);
			
		return add_filter( (string)$tag, $function_to_add, (int)$priority, (int)$accepted_args );
	}
	
	/* 
	 * Hook for remove_filter()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function remove_filter($tag, $function_to_remove, $priority = 10){
		if(!is_array($function_to_remove))
			$function_to_remove = array(&$this, $function_to_remove);
			
		return remove_filter( (string)$tag, $function_to_remove, (int)$priority );
	}
	
	/* 
	 * Hook for add_shortcode()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_shortcode($tag, $function_to_add){
		if(!is_array($function_to_add))
			$function_to_add = array(&$this, $function_to_add);
		
		if(!shortcode_exists($tag)) {
			return add_shortcode( $tag, $function_to_add );
		}
		
		return false;
	}
	
	/* 
	 * Hook for add_options_page()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_options_page($page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null){
		if(!is_array($function))
			$function = array(&$this, $function);
		
		return add_options_page($page_title, $menu_title, $capability, $menu_slug, $function, $position);
	}
	
	/* 
	 * Hook for add_settings_section()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_settings_section($id, $title, $callback, $page){
		if(!is_array($callback))
			$callback = array(&$this, $callback);
		
		return add_settings_section($id, $title, $callback, $page);
	}
	
	/* 
	 * Hook for register_setting()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function register_setting($option_group, $option_name, $args = array()){
		if(!is_array($args) && is_callable($args))
			$args = array(&$this, $args);
		
		return register_setting($option_group, $option_name, $args);
	}
	
	/* 
	 * Hook for add_settings_field()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()){
		if(!is_array($callback))
			$callback = array(&$this, $callback);
		
		return add_settings_field($id, $title, $callback, $page, $section, $args);
	}
	
	/* 
	 * Generate unique token
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function generate_token($length=16){
		if(function_exists('openssl_random_pseudo_bytes') || function_exists('random_bytes'))
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>='))
				return substr(str_rot13(bin2hex(random_bytes(ceil($length * 2)))), 0, $length);
			else
				return substr(str_rot13(bin2hex(openssl_random_pseudo_bytes(ceil($length * 2)))), 0, $length);
		}
		else
		{
			return substr(str_replace(array('.',' ','_'),mt_rand(1000,9999),uniqid('t'.microtime())), 0, $length);
		}
	}
}
endif;

/*
 * Serbian transliteration requirements
 * @since     1.0.0
 * @verson    1.0.0
 */
require_once RSTR_INC . '/Requirements.php';
$Serbian_Transliteration_Activate = new Serbian_Transliteration_Requirements(array('file' => RSTR_FILE));

/*
 * Initialize active plugin
 * @since     1.0.0
 * @verson    1.0.0
 */
if(!class_exists('Serbian_Transliteration_Init') && class_exists('Serbian_Transliteration')) :
final class Serbian_Transliteration_Init extends Serbian_Transliteration {
	
	private static $instance = NULL;
	
	/**
	 * Get singleton instance of global class
	 * @since     7.4.0
	 * @version   7.4.0
	 */
	private static function get_instance()
	{
		if( NULL === self::$instance )
		{
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	public static function run () {
		// Load instance
		$inst = self::get_instance();
		
		if( is_admin() )
		{
			// Load settings page
			require_once RSTR_INC . '/Settings.php';
			new Serbian_Transliteration_Settings();
		}
		
		// Load options
		$options = get_option( RSTR_NAME );
		
		// Load shortcodes
		require_once RSTR_INC . '/Shortcodes.php';
		new Serbian_Transliteration_Shortcodes($options);
		
		// Initialize plugin mode
		if(isset($options['mode']) && $options['mode'] && in_array( $options['mode'], array_keys($inst->plugin_mode()), true ) !== false)
		{
			if($options['transliteration-mode'] != 'none')
			{
				$mode = ucfirst($options['mode']);
				$class_require = "Serbian_Transliteration_Mode_{$mode}";
				$path_require = "Mode_{$mode}";
				$path = apply_filters('serbian_transliteration_class_mode_path', RSTR_INC, $class_require, $options['mode']);
				
				if(file_exists($path . "/{$path_require}.php"))
				{
					require_once $path . "/{$path_require}.php";
					if(class_exists($class_require)){
						new $class_require($options);
					}
				}
				
				// Clear memory
				$class_require = $path_require = $path = $mode = NULL;
			}
		}
	}
}
endif;

if(class_exists('Serbian_Transliteration_Init') && $Serbian_Transliteration_Activate->passes()) :
	/* Do translations
	====================================*/
	add_action('plugins_loaded', function () {
		$locale = apply_filters( 'plugin_locale', get_locale(), RSTR_NAME );
		if ( $loaded = load_textdomain( RSTR_NAME, RSTR_ROOT . '/languages' . '/' . RSTR_NAME . '-' . $locale . '.mo' ) ) {
			return $loaded;
		} else {
			load_plugin_textdomain( RSTR_NAME, FALSE, RSTR_ROOT . '/languages' );
		}
	});
		
	/* Activate plugin
	====================================*/
	Serbian_Transliteration::register_activation_hook(function(){
		$success = true;
		
		// Add activation date
		if($activation = get_site_option(RSTR_NAME . '-activation')) {
			$activation[] = date('Y-m-d H:i:s');
			update_site_option(RSTR_NAME . '-activation', $activation);
		} else {
			add_site_option(RSTR_NAME . '-activation', array(date('Y-m-d H:i:s')));
		}
		
		// Generate unique ID
		if(!get_option(RSTR_NAME . '-ID')) {
			add_site_option(RSTR_NAME . '-ID', Serbian_Transliteration::generate_token(64));
		}

	    return $success;	
	});
	
	/* Deactivate plugin
	====================================*/
	Serbian_Transliteration::register_deactivation_hook(function(){
		// Add deactivation date
		if($deactivation = get_site_option(RSTR_NAME . '-deactivation')) {
			$deactivation[] = date('Y-m-d H:i:s');
			update_site_option(RSTR_NAME . '-deactivation', $deactivation);
		} else {
			add_site_option(RSTR_NAME . '-deactivation', array(date('Y-m-d H:i:s')));
		}
	});
	
	/* Run plugin
	====================================*/
	add_action('init', array('Serbian_Transliteration_Init', 'run'));
endif;