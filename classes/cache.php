<?php if ( !defined('WPINC') ) die();

/**
 * Cache Control
 *
 * @link              http://infinitumform.com/
 * @since             1.4.1
 * @package           RSTR
 * @autor             Ivijan-Stefan Stipic
 */
class Transliteration_Cache {

	/*
	 * Save all cached objcts to this variable
	*/
	private static $cache = [];

	/*
	 * Get cached object
	 *
	 * Returns the value of the cached object, or false if the cache key doesn’t exist
	*/
	public static function get($key, $default = NULL) {
		$key = self::key($key);
		
		if (array_key_exists($key, self::$cache)) {
			return self::$cache[ $key ];
		}
		
		return $default;
	}

	/*
	 * Save object to cache
	 *
	 * This function adds data to the cache if the cache key doesn’t already exist.
	 * If it does exist, the data is not added and the function returns
	*/
	public static function add($key, $value) {
		$key = self::key($key);
		if (!array_key_exists($key, self::$cache)) {
			self::garbage_cleaner();
			self::$cache[ $key ] = $value;
		}
		return self::$cache[ $key ];
	}

	/*
	 * Save object to cache
	 *
	 * Adds data to the cache. If the cache key already exists, then it will be overwritten;
	 * if not then it will be created.
	*/
	public static function set($key, $value, $expire=0) {
		$key = self::key($key);
		self::garbage_cleaner();
		self::$cache[ $key ] = $value;
		return self::$cache[ $key ];
	}

	/*
	 * Replace cached object
	 *
	 * Replaces the given cache if it exists, returns false otherwise.
	*/
	public static function replace($key, $value, $expire=0) {
		$key = self::key($key);
		if (array_key_exists($key, self::$cache)) {
			self::$cache[ $key ] = $value;
		}
		return self::$cache[ $key ];
	}

	/*
	 * Delete cached object
	 *
	 * Clears data from the cache for the given key.
	*/
	public static function delete($key) {
		$key = self::key($key);
		if (array_key_exists($key, self::$cache)) {
			unset(self::$cache[ $key ]);
		}
	}

	/*
	 * Clears all cached data
	*/
	public static function flush() {
		self::$cache = NULL;
		return true;
	}

	/*
	 * Debug cache
	*/
	public static function debug() {
		ob_start();
		var_dump(self::$cache);
		$debug = ob_get_clean();
		echo '<pre class="rstr-cache-debug">' . htmlspecialchars(preg_replace(array(
			'/(\=\>\n\s{2,4})/'
		) , array(
			' => '
		) , $debug)) . '</pre>';
	}

	/*
	 * Cache key
	*/
	private static function key($key) {
		static $suffix = null;

		if ($suffix === null) {
			$suffix = strtr((string)microtime(true), '.', '');
		}

		return "{$key}_{$suffix}";
	}

	/*
	 * PRIVATE: Clean up the accumulated garbage
	*/
	private static function garbage_cleaner() {
		if (!is_array(self::$cache)) {
			return;
		}

		$capability = apply_filters('rstr/cache/capability', (defined('RSTR_CACHE_CAPABILITY') 
						? RSTR_CACHE_CAPABILITY 
							: 100), self::$cache );
							
		$gc_probability = apply_filters('rstr/cache/gc_probability', (defined('RSTR_CACHE_GARBAGE_COLLECTION_PROBABILITY') 
							? RSTR_CACHE_GARBAGE_COLLECTION_PROBABILITY 
								: 1), self::$cache );
								
		$gc_divisor = apply_filters('rstr/cache/gc_divisor', (defined('RSTR_CACHE_GARBAGE_COLLECTION_DIVISOR') 
						? RSTR_CACHE_GARBAGE_COLLECTION_DIVISOR 
							: 100), self::$cache);
		
		$fn_rand = function_exists('random_int') ? 'random_int' : 'mt_rand';
		
		if ($fn_rand(1, $gc_divisor) && ($gc_probability / $gc_divisor)) {
			while (count(self::$cache) > $capability) {
				reset(self::$cache);
				$key = key(self::$cache);
				
				if( !in_array($key, self::$cache) ) {
					self::delete($key);
				}
			}
		}
		
	}
}