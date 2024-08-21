<?php if ( !defined('WPINC') ) die();

class Transliteration_Search extends Transliteration {
    
    public function __construct() {		
		$this->add_filter('posts_search', 'posts_search', 10, 2);
    }
	
	public function posts_search($search, $wp_query)
    {
        global $wpdb;

        if (empty($search)) {
            return $search;
        }

        $q = $wp_query->query_vars;
        $n = !empty($q['exact']) ? '' : '%';
        $search_terms = $q['search_terms'];
        $exclusion_prefix = apply_filters('wp_query_search_exclusion_prefix', '-');
        $search_mode = get_rstr_option('search-mode', 'auto');

        $search_queries = [];
        $searchand = '';

        foreach ($search_terms as $term) {
            $term_transliterated = $this->transliterate_term($term, $search_mode);
            list($like_op, $andor_op, $term, $term_transliterated) = $this->prepare_term($term, $term_transliterated, $exclusion_prefix);

            $search_queries[] = $this->build_search_query($wpdb, $like_op, $andor_op, $n, $term, $term_transliterated, $searchand);
            $searchand = ' AND ';
        }

        $search = $this->finalize_search_query($wpdb, $search_queries);
		
		
		
        return $search;
    }

    private function transliterate_term($term, $search_mode)
    {
        if ($search_mode == 'auto') {
            if (Transliteration_Utilities::is_cyr($term)) {
                return Transliteration_Controller::get()->cyr_to_lat($term, false);
            } else {
                return Transliteration_Controller::get()->lat_to_cyr($term, false, (get_rstr_option('fix-diacritics', 'no') != 'no'));
            }
        } else if ($search_mode == 'plugin-mode') {
			
			if(get_rstr_option('site-script', 'lat') == 'cyr') {
				return Transliteration_Controller::get()->lat_to_cyr($term, false, get_rstr_option('fix-diacritics', 'no') == 'yes');
			} else {
				return Transliteration_Controller::get()->cyr_to_lat($term);
			}
        }
    }

    private function prepare_term($term, $term_transliterated, $exclusion_prefix)
    {
        $exclude = $exclusion_prefix && (substr($term??'', 0, 1) === $exclusion_prefix);
        if ($exclude) {
            $like_op = 'NOT LIKE';
            $andor_op = 'AND';
            $term = substr($term??'', 1);
            $term_transliterated = substr($term_transliterated??'', 1);
        } else {
            $like_op = 'LIKE';
            $andor_op = 'OR';
        }
        return array($like_op, $andor_op, $term, $term_transliterated);
    }

    private function build_search_query($wpdb, $like_op, $andor_op, $n, $term, $term_transliterated, $searchand)
	{
		$like = $n . $wpdb->esc_like($term) . $n;
		$tr_like = $n . $wpdb->esc_like($term_transliterated) . $n;

		if ($term === $term_transliterated) {
			// Ako su term i term_transliterated isti, nema potrebe za dupliranjem uslova
			return $wpdb->prepare("{$searchand}(
				{$wpdb->posts}.post_title {$like_op} %s
				{$andor_op}
				{$wpdb->posts}.post_excerpt {$like_op} %s
				{$andor_op}
				{$wpdb->posts}.post_content {$like_op} %s
			)", $like, $like, $like);
		} else {
			// Ako su term i term_transliterated različiti, generišemo uslov za oba
			return $wpdb->prepare("{$searchand}(
				(
					{$wpdb->posts}.post_title {$like_op} %s
					{$andor_op}
					{$wpdb->posts}.post_excerpt {$like_op} %s
					{$andor_op}
					{$wpdb->posts}.post_content {$like_op} %s
				)
				OR
				(
					{$wpdb->posts}.post_title {$like_op} %s
					{$andor_op}
					{$wpdb->posts}.post_excerpt {$like_op} %s
					{$andor_op}
					{$wpdb->posts}.post_content {$like_op} %s
				)
			)", $like, $like, $like, $tr_like, $tr_like, $tr_like);
		}
	}

    private function finalize_search_query($wpdb, $search_queries)
    {
        $search = implode(' ', $search_queries);
        if (!empty($search)) {
            $search = " AND ({$search}) ";
            if (!is_user_logged_in()) {
                $search .= " AND ({$wpdb->posts}.post_password = '') ";
            }
        }
        return $search;
    }
	
}