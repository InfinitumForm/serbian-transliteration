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
	public $cap = 100;
    public $gcProbability = 1;
    public $gcDivisor = 100;
    private $cache = array();

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
		return $this->cache[$key];
    }
	
	/*
	 * Delete cached object
	 */
	public function delete($key)
    {
        if(isset($this->cache[$key])){
			unset($this->cache[$key]);
			return true;
		}
		return false;
    }

	/*
	 * Debug cache
	 */
	public function debug()
	{
		echo '<pre class="rstr-cache-debug">', var_dump($this->cache), '</pre>';
	}

	/*
	 * PRIVATE: Collect garbage
	 */
    private function collect_garbage()
    {   

        if ((mt_rand() / mt_getrandmax()) && ($this->gcProbability / $this->gcDivisor))
		{
			while (count($this->cache) > $this->cap)
			{
				reset($this->cache);
				$key = key($this->cache);
				$this->remove($key);
			}
		}
    }
}
endif;