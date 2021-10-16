<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Cache Control
 *
 * @link              http://infinitumform.com/
 * @since             1.4.1
 * @package           RSTR
 * @autor             Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Cache')) :
class Serbian_Transliteration_Cache
{
	private static $cache = NULL;
	
	// Cache group
	const GROUP = 'serbian-transliteration';
	
	/*
	 * Add a group to the list of global groups
	 */
	public static function instance(){
	
	}
	
	/*
	 * Cache prefix
	 */
	public static function prefix($key) {
		static $prefix;
		
		$key = trim($key);

		if ( empty($prefix) ) {
			$prefix = str_replace('.', '', (string)microtime(true));
		}

		return "rstr_cache_{$prefix}__{$key}";
	}

	/*
	 * Get cached object
	 *
	 * Returns the value of the cached object, or false if the cache key doesn’t exist
	 */
    public static function get($key)
    {
        return self::$cache[ self::prefix($key) ] ?? NULL;
    }
	
	/*
	 * Save object to cache
	 *
	 * This function adds data to the cache if the cache key doesn’t already exist.
	 * If it does exist, the data is not added and the function returns
	 */
    public static function add($key, $value)
    {   
		if(!isset(self::$cache[ self::prefix($key) ])) {
			self::$cache[ self::prefix($key) ] = $value;
		}
		return self::$cache[ self::prefix($key) ];
    }

	/*
	 * Save object to cache
	 *
	 * Adds data to the cache. If the cache key already exists, then it will be overwritten;
	 * if not then it will be created.
	 */
    public static function set($key, $value, $expire=0)
    {
		self::$cache[ self::prefix($key) ] = $value;
		return self::$cache[ self::prefix($key) ];
    }
	
	/*
	 * Replace cached object
	 *
	 * Replaces the given cache if it exists, returns false otherwise.
	 */
    public static function replace($key, $value, $expire=0)
    {
        if(isset(self::$cache[ self::prefix($key) ])) {
			self::$cache[ self::prefix($key) ] = $value;
		}
		return self::$cache[ self::prefix($key) ];
    }
	
	/*
	 * Delete cached object
	 *
	 * Clears data from the cache for the given key.
	 */
	public static function delete($key)
    {
		if(isset(self::$cache[ self::prefix($key) ])) {
			unset(self::$cache[ self::prefix($key) ]);
		}
    }
	
	/*
	 * Clears all cached data
	 */
	public static function flush()
    {
		self::$cache=NULL;
		return true;
    }
	
	/*
	 * Debug cache
	 */
	public static function debug()
	{
		ob_start();
			var_dump(self::$cache);
		$debug = ob_get_clean();
		echo '<pre class="rstr-cache-debug">' . htmlspecialchars(preg_replace(
			array('/(\=\>\n\s{2,4})/'),
			array(' => '),
			$debug
		)) . '</pre>';
	}
}
endif;