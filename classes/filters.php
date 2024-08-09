<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Filters', false) ) : class Transliteration_Filters extends Transliteration {
    
    public function __construct() {
		$this->add_filter('transliteration_mode_filters', 'exclude_filters', PHP_INT_MAX - 100);
		$this->add_filter('transliteration_init_classes', 'disable_classes');
		
		$this->add_filter('rstr/init/exclude/lat', 'exclude_lat_words');
		$this->add_filter('rstr/init/exclude/cyr', 'exclude_cyr_words');
    }
	
	/*
	 * Exclude filters
	 */
	public function exclude_filters($filters) {
		if( $remove_filters = get_rstr_option('transliteration-filter', []) ) {
			$filters = array_diff_key($filters, array_flip($remove_filters));
		}
		return $filters;
	}
	
	/*
	 * Exclude filters
	 */
	public function disable_classes($classes) {
		$remove = [];

		// Dodajte klase za uklanjanje na osnovu uslova
		if (get_rstr_option('transliteration-mode', 'cyr_to_lat') === 'none') {
			$remove = array_merge($remove, [
				'Transliteration_Email',
				'Transliteration_Ajax',
				'Transliteration_Rest',
				'Transliteration_Search'
			]);
		}

		if (Transliteration_Controller::get()->disable_transliteration()
			|| is_null(Transliteration_Map::get()->map())) {
			$remove = array_merge($remove, [
				'Transliteration_Email',
				'Transliteration_Ajax',
				'Transliteration_Rest'
			]);
		}

		if (get_rstr_option('enable-search', 'no') == 'no') {
			$remove[] = 'Transliteration_Search';
		}

		if (get_rstr_option('force-rest-api', 'yes') == 'no') {
			$remove[] = 'Transliteration_Rest';
		}

		if (get_rstr_option('force-ajax-calls', 'no') == 'no' || !wp_doing_ajax()) {
			$remove[] = 'Transliteration_Ajax';
		}

		if (get_rstr_option('force-email-transliteration', 'no') == 'no') {
			$remove[] = 'Transliteration_Email';
		}
		
		$remove = array_unique($remove);

		$classes = Transliteration_Utilities::array_filter($classes, $remove);

		return $classes;
	}
	
	/*
	 * Exclude latin words
	 */
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
	
	/*
	 * Exclude cyrillic words
	 */
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