<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Cache Control
 *
 * @link              http://infinitumform.com/
 * @since             1.4.1
 * @package           Serbian_Transliteration
 * @autor             Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Cache')) :
class Serbian_Transliteration_Cache
{
	private $cap = 100;
    private $gcProbability = 1;
    private $gcDivisor = 100;
    
	// Collection
	private $cache = array();
	
	public function __construct(){
		if(defined('RSTR_CACHE_CAPABILITY')) 						$this->cap = RSTR_CACHE_CAPABILITY;
		if(defined('RSTR_CACHE_GARBAGE_COLLECTION_PROBABILITY'))	$this->gcProbability = RSTR_CACHE_GARBAGE_COLLECTION_PROBABILITY;
		if(defined('RSTR_CACHE_GARBAGE_COLLECTION_DIVISOR'))		$this->gcDivisor = RSTR_CACHE_GARBAGE_COLLECTION_DIVISOR;
	}

	/*
	 * Get cached object
	 */
    public function get($key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : false;
    }

	/*
	 * Save object to cache
	 */
    public function set($key, $value)
    {   
        $this->cache[$key] = $value;
        $this->collect_garbage();
		return $this->get($key);
    }
	
	/*
	 * Delete cached object
	 */
	public function delete($key)
    {
		if(is_array($key))
		{
			$i = 0;
			foreach($key as $k){
				if(isset($this->cache[$k])){
					unset($this->cache[$k]);
					++$i;
				}
			}
			return ($i > 0);
		}
		else
		{
			if(isset($this->cache[$key])){
				unset($this->cache[$key]);
				return true;
			}
		}
		return false;
    }

	/*
	 * Debug cache
	 */
	public function debug()
	{
		echo '<pre class="rstr-cache-debug">', var_dump(
			array_merge(array(
				'Serbian_Transliteration_Cache' => array(
					'capability' => $this->cap,
					'garbage_collection_probability' => $this->gcProbability,
					'garbage_collection_divisor' => $this->gcDivisor,
					'cache_objects_length' => count($this->cache)
				)
			), $this->cache)
		), '</pre>';
	}

	/*
	 * PRIVATE: Collect garbage
	 */
    private function collect_garbage()
    {   
		if(function_exists('mt_rand')) {
			$getrandmax = mt_getrandmax();
			$rand = mt_rand();
		} else {
			$getrandmax = getrandmax();
			$rand = rand();
		}
		
        if (($rand / $getrandmax) && ($this->gcProbability / $this->gcDivisor))
		{
			$this->clear_garbage();
		}
    }
	
	/*
	 * PRIVATE: Clear garbage
	 */
    private function clear_garbage()
    {   
		while (count($this->cache) > $this->cap)
		{
			reset($this->cache);
			unset($this->cache[key($this->cache)]);
		}
	}
}
endif;