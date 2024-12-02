<?php
/**
 * Trait that provides caching functionality for an object.
 *
 * @link              http://infinitumform.com/
 * @since             1.4.1
 * @package           RSTR
 * @autor             Ivijan-Stefan Stipic
 */
trait Transliteration__Cache {
    // Static cache for use in static functions
    protected static array $static_data = [];
    
    // Instance cache for use in non-static functions
    protected array $data = [];

    /**
     * Get data from cached data within this Object or Class
     *
     * @param string $key
     * @param callable $function
     * @param string|null $index
     * @return mixed
     */
    protected static function cached_static(string $key, callable $function, $index = null) {
        // Add prefix to key
        $key = self::cache_key($key, $index);

        // Return cached value if it exists
        if (array_key_exists($key, self::$static_data)) {
            return self::$static_data[$key];
        }

        // Generate and cache the value
        self::$static_data[$key] = $function();

        return self::$static_data[$key];
    }

    /**
     * Get data from cached data within this Object
     *
     * @param string $key
     * @param callable $function
     * @param string|null $index
     */
    protected function cached(string $key, callable $function, $index = null) {

        // Add prefix to key
        $key = self::cache_key($key, $index);

        // Return cached value if it exists
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        // Generate and cache the value
        $this->data[$key] = $function();

        return $this->data[$key];
    }

    /**
     * Clear cached value for a specific key
     *
     * @param string $key
     * @param string|null $prefix
     * @return void
     */
    protected static function clear_static_cache(string $key, $index = null): void {
        // Add prefix to key
        $key = self::cache_key($key, $index);

        if (isset(self::$static_data[$key])) {
            unset(self::$static_data[$key]);
        }
    }

    /**
     * Clear cached value for a specific key
     *
     * @param string $key
     * @param string|null $prefix
     * @return void
     */
    protected function clear_cache(string $key, $index = null): void {
        // Add prefix to key
        $key = self::cache_key($key, $index);

        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    /**
     * Clear all static cached values
     *
     * @param bool $is_static
     * @return void
     */
    protected static function clear_static_cache_all(): void {
        self::$static_data = [];
    }

    /**
     * Clear all cached values
     *
     * @return void
     */
    public function clear_cache_all() : void
    {
        $this->data = [];
    }

    /**
     * Get all static cached data within this object or class.
     *
     * @return array The cached data.
     */
    protected static function get_all_static_cached(): array {
        return self::$static_data;
    }

    /**
     * Get all cached data within this object.
     *
     * @return array The cached data.
     */
    protected function get_all_cached() : array {
        return $this->data;
    }

    /**
	 * Generates a cache key based on the class name and the provided key.
	 *
	 * @param string $key The key to use for the cache.
	 * @param string|int|array|null $index An optional index to include in the cache key.
	 * @return string The generated cache key.
	 */
	private static array $key_cache = [];
	private static function cache_key(string $key, $index = null): string
	{
		// Generate a unique identifier for the cache
		$cacheKey = $key . '|' . (is_array($index) ? implode('.', self::cache_array_key($index)) : (string)($index ?? ''));

		// Return cached result if it exists
		if (isset(self::$key_cache[$cacheKey])) {
			return self::$key_cache[$cacheKey];
		}

		// Determine the root context (class or calling function)
		$root = class_exists(static::class) 
			? static::class 
			: (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'] ?? 'global');

		// Generate the base key with sanitized key value
		$generatedKey = $root . '::' . preg_replace('/[^a-zA-Z0-9_\-:]/', '_', $key);

		// Process the index if provided
		if ($index !== null) {
			if (is_array($index)) {
				// Properly flatten and sanitize array values
				$index = implode('.', array_map(
					fn($v) => preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)$v),
					self::cache_array_key($index)
				));
			} else {
				// Sanitize single value
				$index = preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)$index);
			}
			$generatedKey .= '->' . $index;
		}

		// Cache the result for future use
		self::$key_cache[$cacheKey] = $generatedKey;

		return $generatedKey;
	}

    /**
	 * Recursively extract all values from a nested array.
	 *
	 * @param array $array
	 * @return array
	 */
	private static function cache_array_key(array $array): array
	{
		$flatValues = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				// If value is an array, recursively extract its values
				$flatValues = array_merge($flatValues, self::cache_array_key($value));
			} else {
				if (empty($value)) {
					$value = is_numeric($key) ? 'null' : $key;
				}

				// Append sanitized value
				$flatValues[] = (string)$value;
			}
		}
		return $flatValues;
	}

}