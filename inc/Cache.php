<?php if (!defined('WPINC')) {
    die("Don't mess with us.");
}
/**
 * Cache Control
 *
 * @link              http://infinitumform.com/
 * @since             1.4.1
 * @package           RSTR
 * @autor             Ivijan-Stefan Stipic
 */
if (!class_exists('Serbian_Transliteration_Cache', false)):
    class Serbian_Transliteration_Cache {

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
			
			if (isset(self::$cache[ $key ])) {
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
			if (!isset(self::$cache[$key])) {
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
			if (isset(self::$cache[$key])) {
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
            if (isset(self::$cache[$key])) {
                unset(self::$cache[ self::key($key) ]);
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

			$capability = defined('RSTR_CACHE_CAPABILITY') ? RSTR_CACHE_CAPABILITY : apply_filters('rstr/cache/capability', 100);
			$gc_probability = defined('RSTR_CACHE_GARBAGE_COLLECTION_PROBABILITY') ? RSTR_CACHE_GARBAGE_COLLECTION_PROBABILITY : apply_filters('rstr/cache/gc_probability', 1);
			$gc_divisor = defined('RSTR_CACHE_GARBAGE_COLLECTION_DIVISOR') ? RSTR_CACHE_GARBAGE_COLLECTION_DIVISOR : apply_filters('rstr/cache/gc_divisor', 100);

			if ((mt_rand(1, $gc_divisor) <= $gc_probability) && (count(self::$cache) > $capability) ) {
				uasort(self::$cache, function ($a, $b) {
					return $a['last_access_time'] <=> $b['last_access_time'];
				});
				self::$cache = array_slice(self::$cache, 0, $capability, true);
			}
		}
    }
endif;