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
	// Cache group
	const GROUP = 'serbian-transliteration';
	
	/*
	 * Add a group to the list of global groups
	 */
	public static function instance(){
		wp_cache_add_global_groups(self::GROUP);
	}
	
	/*
	 * Cache prefix
	 */
	public static function prefix($key = '') {
		$key = trim($key);
		$name = self::GROUP . '_cache_prefix__';
		$prefix = wp_cache_get( $name, self::GROUP );

		if ( false === $prefix ) {
			$prefix = str_replace('.', '', (string)microtime(true));
			wp_cache_set( $name, $prefix, self::GROUP );
		}

		return "rstr_cache_{$prefix}__{$key}";
	}

	/*
	 * Get cached object
	 *
	 * Returns the value of the cached object, or false if the cache key doesn’t exist
	 */
    public static function get($key, $force = false, $found = NULL)
    {
        return wp_cache_get(self::prefix($key), self::GROUP, $force, $found);
    }
	
	/*
	 * Save object to cache
	 *
	 * This function adds data to the cache if the cache key doesn’t already exist.
	 * If it does exist, the data is not added and the function returns
	 */
    public static function add($key, $value, $expire=0)
    {   
		return (wp_cache_add( self::prefix($key), $value, self::GROUP, $expire )!==false ? $value : false);
    }

	/*
	 * Save object to cache
	 *
	 * Adds data to the cache. If the cache key already exists, then it will be overwritten;
	 * if not then it will be created.
	 */
    public static function set($key, $value, $expire=0)
    {   
		return (wp_cache_set( self::prefix($key), $value, self::GROUP, $expire )!==false ? $value : false);
    }
	
	/*
	 * Replace cached object
	 *
	 * Replaces the given cache if it exists, returns false otherwise.
	 */
    public static function replace($key, $value, $expire=0)
    {
        return (wp_cache_replace( self::prefix($key), $value, self::GROUP, $expire )!==false ? $value : false);
    }
	
	/*
	 * Delete cached object
	 *
	 * Clears data from the cache for the given key.
	 */
	public static function delete($key)
    {
		return wp_cache_delete(self::prefix($key), self::GROUP)!==false;
    }
	
	/*
	 * Clears all cached data
	 */
	public static function flush()
    {
		global $wp_object_cache;
		if($wp_object_cache && isset($wp_object_cache->cache[self::GROUP])){
			unset($wp_object_cache->cache[self::GROUP]);
			return true;
		}
		return false;
    }
	
	/*
	 * Debug cache
	 */
	public static function debug()
	{
		global $wp_object_cache;
		$debug = array();
		if(isset($wp_object_cache->cache[self::GROUP])) {
			ob_start();
				var_dump($wp_object_cache->cache[self::GROUP]);
			$debug = ob_get_clean();
		}
		echo '<pre class="rstr-cache-debug">' . htmlspecialchars(preg_replace(
			array('/(\=\>\n\s{2,4})/'),
			array(' => '),
			$debug
		)) . '</pre>';
	}
}
endif;