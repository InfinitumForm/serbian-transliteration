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
if(!class_exists('Serbian_Transliteration_DB_Cache')) : class Serbian_Transliteration_DB_Cache {
	
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

		if( !self::table_exists() ) {
			if( $transient = get_transient( $key ) ) {
				return $transient;
			} else {
				return $default;
			}
		}
		
		global $wpdb;
		
		if( !empty($key) && ($result = $wpdb->get_var( $wpdb->prepare("
			SELECT
				`{$wpdb->rstr_cache}`.`value`
			FROM
				`{$wpdb->rstr_cache}`
			WHERE
				`{$wpdb->rstr_cache}`.`key` = %s
		", $key ))) ) {
			if(is_serialized($result) || self::is_serialized($result)){
				$result = unserialize($result);
			}
			return $result;
		}
		
		return $default;
	}
	
	/*
	 * Save object to cache
	 *
	 * This function adds data to the cache if the cache key doesn’t already exist.
	 * If it does exist returns false, if not save it return NULL
	 */
    public static function add(string $key, $value, int $expire = 0) {
		
		$key = self::key($key);
		
		if(self::get($key, NULL) === NULL) {
			
			if( !self::table_exists() ) {
				$save = set_transient( $key, $value, $expire );
			} else {
				if($expire > 0) {
					$expire = (time()+$expire);
				}
				
				if(is_array($value) || is_object($value) || is_bool($value)){
					$value = serialize($value);
				}
				
				global $wpdb;
				
				$save = $wpdb->query( $wpdb->prepare("
					INSERT INTO `{$wpdb->rstr_cache}` (`key`, `value`, `expire`)
					VALUES (%s, %s, %d)
				", $key, $value, $expire ));
			}
			
			if($save && !is_wp_error($save)){
				return $value;
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
		
		$key = self::key($key);
		
		if( !self::table_exists() ) {
			if( set_transient( $key, $value, $expire ) ) {
				return $value;
			} else {
				return NULL;
			}
		}
		
		if( !self::add($key, $value, $expire) ) {
			if( self::replace($key, $value, $expire) ) {
				return $value;
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
			
			if(is_array($value) || is_object($value) || is_bool($value)){
				$value = serialize($value);
			}
			
			global $wpdb;
			
			$save = $wpdb->query( $wpdb->prepare("
				UPDATE `{$wpdb->rstr_cache}`
				SET `value` = %s, `expire` = %d
				WHERE `key` = %s
			", $value, $expire, $key ));
		}
		
		if($save && !is_wp_error($save)){
			return $value;
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
		
		return $wpdb->query( $wpdb->prepare("DELETE FROM `{$wpdb->rstr_cache}` WHERE `key` = %s", $key ));
    }
	
	/*
	 * Clears all cached data
	 */
	public static function flush() {
		
		if( !self::table_exists() ) {
			Serbian_Transliteration_Utilities::clear_plugin_cache();
			return true;
		}
		
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
		if( !self::table_exists(true) ) {
			global $wpdb;
			
			// Include important library
			if(!function_exists('dbDelta')){
				require_once ABSPATH . DIRECTORY_SEPARATOR . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php';
			}
			
			// Install table
			$charset_collate = $wpdb->get_charset_collate();
			dbDelta("
			CREATE TABLE IF NOT EXISTS {$wpdb->rstr_cache} (
				`key` varchar(255) NOT NULL,
				`value` text NOT NULL,
				`expire` int(11) NOT NULL DEFAULT 0,
				UNIQUE KEY `cache_key` (`key`),
				KEY `cache_expire` (`expire`)
			) {$charset_collate}
			");
		}
	}
	
	/*
	 * Check is value is serialized
	 * @verson    1.0.0
	 */
	private static function is_serialized( $data, $strict = true ) {
		// If it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( 'N;' === $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace ) {
				return false;
			}
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 ) {
				return false;
			}
			if ( false !== $brace && $brace < 4 ) {
				return false;
			}
		}
		$token = $data[0];
		switch ( $token ) {
			case 's':
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
				// Or else fall through.
			case 'a':
			case 'O':
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b':
			case 'i':
			case 'd':
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E+-]+;{$end}/", $data );
		}
		return false;
	}
	
	/*
	 * Instance
	 * @verson    1.0.0
	 */
	public static function instance() {
		$class = self::class;
		$instance = CFGP_Cache::get($class);
		if ( !$instance ) {
			$instance = CFGP_Cache::set($class, new self());
		}
		return $instance;
	}
	
} endif;