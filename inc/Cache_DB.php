<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Database Cache Control
 *
 * @link          http://infinitumform.com/
 * @since         1.8.1
 * @package       RSTR
 * @author        Ivijan-Stefan Stipic
 * @version       1.0.0
 */
if(!class_exists('Serbian_Transliteration_DB_Cache', false)) : class Serbian_Transliteration_DB_Cache {
	/*
	 * Save all cached objcts to this variable
	 */
	private static $cache = [];
	
	/*
	 * Main instance variable for memory cache
	 */
	protected static $__instance;
	
	/*
	 * Main constructor
	 */
	private function __construct() {
		if( self::table_exists() ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare("DELETE FROM `{$wpdb->rstr_cache}` WHERE `expire` != 0 AND `expire` <= %d", time() ));
		}
	}
	
	/*
	 * Get cached object
	 *
	 * Returns the value of the cached object, or false if the cache key doesn’t exist
	 */
	public static function get( string $key, $default=NULL ) {
		
		$key = self::key($key);
		
		if (array_key_exists($key, self::$cache)) {
			return maybe_unserialize(self::$cache[$key]);
		}

		if( !self::table_exists() ) {
			if( $transient = get_transient( $key ) ) {
				self::$cache[$key] = $transient;
			} else {
				self::$cache[$key] = $default;
			}
			
			return maybe_unserialize(self::$cache[$key]);
		}
		
		global $wpdb;
		
		if( !empty($key) && ($result = $wpdb->get_var( $wpdb->prepare("
			SELECT `{$wpdb->rstr_cache}`.`value` 
			FROM `{$wpdb->rstr_cache}` 
			WHERE `{$wpdb->rstr_cache}`.`key` = %s
		", $key ))) ) {
			$result = maybe_unserialize($result);
			self::$cache[$key] = $result;
			
			return self::$cache[$key];
		}
		
		self::$cache[$key] = $default;
		
		return self::$cache[$key];
	}
	
	/*
	 * Save object to cache
	 *
	 * This function adds data to the cache if the cache key doesn’t already exist.
	 * If it does exist returns false, if not save it return NULL
	 */
    public static function add(string $key, $value, int $expire = 0) {
		
		if(self::get($key, NULL) === NULL) {
			$key = self::key($key);
			
			if( !self::table_exists() ) {
				$save = set_transient( $key, $value, $expire );
			} else {
				if($expire > 0) {
					$expire = (time()+$expire);
				}
				
				$value = maybe_serialize($value);
				
				global $wpdb;
				
				$save = $wpdb->query( $wpdb->prepare("
					INSERT INTO `{$wpdb->rstr_cache}` (`key`, `value`, `expire`)
					VALUES (%s, %s, %d)
				", $key, $value, $expire ));
			}
			
			if($save && !is_wp_error($save)){
				self::$cache[$key] = $value;
				return self::$cache[$key];
			}
			
			return NULL;
		}
		
		return false;
    }
	
	/*
	 * Save object to cache
	 *
	 * Adds data to the cache. If the cache key already exists, then it will be overwritten;
	 * if not then it will be created.
	 */
    public static function set(string $key, $value, int $expire=0) {
		
		if( empty($value) ) {
			return NULL;
		}
		
		if( $value == ($existing_value = self::get($key, NULL)) ) {
			return $existing_value;
		} else {			
			if( $return = self::add($key, $value, $expire) ) {
				return $return;
			} else if( $return = self::replace($key, $value, $expire) ) {
				return $return;
			}
		}
		
		return NULL;
    }
	
	/*
	 * Replace cached object
	 *
	 * Replaces the given cache if it exists, returns NULL otherwise.
	 */
    public static function replace(string $key, $value, int $expire=0) {
		
		if( empty($value) ) {
			sef::delete($key);
			return NULL;
		}
		
		$key = self::key($key);
		
		if( !self::table_exists() ) {
			$save = set_transient( $key, $value, $expire );
		} else {
			if($expire > 0) {
				$expire = (time()+$expire);
			}
			
			$value = maybe_serialize($value);
			
			global $wpdb;
			
			$save = $wpdb->query( $wpdb->prepare("
				UPDATE `{$wpdb->rstr_cache}`
				SET `value` = %s, `expire` = %d
				WHERE `key` = %s
			", $value, $expire, $key ));
		}
		
		if($save && !is_wp_error($save)){
			self::$cache[$key] = $value;
			return self::$cache[$key];
		}
		
		return NULL;
    }
	
	/*
	 * Delete cached object
	 *
	 * Clears data from the cache for the given key.
	 */
	public static function delete(string $key) {
		
		$key = self::key($key);
		
		if( !self::table_exists() ) {
			return delete_transient( $key );
		}
		
		global $wpdb;
		
		
		if(isset(self::$cache[$key])) {
			unset(self::$cache[$key]);
		}
		
		return $wpdb->query( $wpdb->prepare(
			"DELETE FROM `{$wpdb->rstr_cache}` 
			WHERE `key` = %s", $key 
		));
    }
	
	/*
	 * Clears all cached data
	 */
	public static function flush() {
		
		if( !self::table_exists() ) {
			Serbian_Transliteration_Utilities::clear_plugin_cache();
			return true;
		}
		
		self::$cache = [];
		
		global $wpdb;
		return $wpdb->query("TRUNCATE TABLE `{$wpdb->rstr_cache}`");
    }
	
	/*
	 * Cache key
	 */
	private static function key(string $key) {
		$key = trim($key);
		$key = stripslashes($key);
		
		return str_replace(
			array(
				'.',
				"\s",
				"\t",
				"\n",
				"\r",
				'\\',
				'/'
			),
			array(
				'_',
				'-',
				'-',
				'-',
				'-',
				'_',
				'_'
			),
			$key
		);
	}
	
	/*
	 * Check is database table exists
	 * @verson    1.0.0
	 */
	public static function table_exists($dry = false) {
		static $table_exists = NULL;
		global $wpdb;
		
		if(NULL === $table_exists || $dry) {
			if($wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->rstr_cache}'" ) != $wpdb->rstr_cache) {
				if( $dry ) {
					return false;
				}
				
				$table_exists = false;
			} else {
				if( $dry ) {
					return true;
				}
				
				$table_exists = true;
			}
		}
		
		return $table_exists;
	}
	
	/*
	 * Install missing tables
	 * @verson    1.0.0
	 */
	public static function table_install() {
		global $wpdb;
		
		if( !self::table_exists(true) ) {	
			// Include important library
			if(!function_exists('dbDelta')){
				require_once ABSPATH . DIRECTORY_SEPARATOR . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php';
			}
			
			// Install table
			$charset_collate = $wpdb->get_charset_collate();
			dbDelta("
			CREATE TABLE IF NOT EXISTS {$wpdb->rstr_cache} (
				`key` varchar(255) NOT NULL,
				`value` longtext NOT NULL,
				`expire` int(11) NOT NULL DEFAULT 0,
				UNIQUE KEY `cache_key` (`key`),
				KEY `cache_expire` (`expire`)
			) {$charset_collate}
			");
		} else if( RSTR_DATABASE_VERSION === '1.0.1' ) {
			$wpdb->query("ALTER TABLE `wp_rstr_cache` CHANGE `value` `value` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");
		}
	}
	
	/*
	 * Instance
	 * @verson    1.0.0
	 */
	public static function instance() {
		if ( !self::$__instance ) {
			self::$__instance = new self();
		}
		return self::$__instance;
	}
	
} endif;