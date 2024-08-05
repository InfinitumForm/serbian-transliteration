<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Filters', false) ) : class Transliteration_Filters extends Transliteration {
    
    public function __construct() {
		$this->add_filter('rstr/init/exclude/lat', 'exclude_lat_words');
		$this->add_filter('rstr/init/exclude/cyr', 'exclude_cyr_words');
    }
	
	public function exclude_lat_words($list) {
		$exclude_latin_words = get_rstr_option('exclude-latin-words', '');
		
		if(!empty($exclude_latin_words)) {
			$array = array();
			if($split = preg_split('/[\n|]/', $exclude_latin_words))
			{
				$split = array_map('trim',$split);
				$split = array_filter($split);
				if(!empty($split) && is_array($split))
				{
					$array = $split;
				}
			}
			return array_merge($list, $array);
		}
		
		return $list;
	}
	
	public function exclude_cyr_words($list) {
		$exclude_cyrillic_words = get_rstr_option('exclude-cyrillic-words', '');
		
		if(!empty($exclude_cyrillic_words)) {
			$array = array();
			if($split = preg_split('/[\n|]/', $exclude_cyrillic_words))
			{
				$split = array_map('trim',$split);
				$split = array_filter($split);
				if(!empty($split) && is_array($split))
				{
					$array = $split;
				}
			}
			return array_merge($list, $array);
		}
		
		return $list;
	}
	
} endif;