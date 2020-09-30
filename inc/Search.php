<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Fix search functionality
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Sefan Stipic
 * @contributor       Igor Milenkovic (@dizajn24)
 */
if(!class_exists('Serbian_Transliteration_Search')) :
class Serbian_Transliteration_Search extends Serbian_Transliteration
{
	/**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct($options)
    {
		$this->options = $options;
        $this->add_filter( 'request', 'request' );
		$this->add_filter( 'get_search_query', 'request' );
    }
	
	public function request ($search_vars) {
		if ( isset($search_vars['s']) && !empty($search_vars['s']) ) {
			$search_vars['s'] = $this->transliterate_text($search_vars['s'], (isset($this->options['site-script']) && $this->options['site-script'] == 'cyr' ? 'lat_to_cyr' : 'cyr_to_lat'));
		}
		return $search_vars;
	}
}
endif;