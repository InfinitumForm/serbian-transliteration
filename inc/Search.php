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
     * Start up
     */
    public function __construct()
    {
  		$this->add_filter('posts_search', 'posts_search', (PHP_INT_MAX-1), 2);
    }

  /**
   * Special WP_Query Transliteration Search
   *
   * @author            Ivijan-Sefan Stipic
   * @since             1.6.0
   * @version           1.0.0
   */
	public function posts_search( $search, $wp_query )
	{
		global $wpdb;

		if(empty($search)) {
			return $search; // skip processing - no search term in query
		}

		$q = $wp_query->query_vars;

		$n = !empty($q['exact']) ? '' : '%';
		$search = $searchand = '';

		$q['search_orderby_title'] = array();

		$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );

    $search_mode = get_rstr_option('search-mode', 'auto');

		foreach ( $q['search_terms'] as $term ) {

      // Let's use 2 modes in this case
      if($search_mode == 'auto') {
        if(Serbian_Transliteration_Utilities::is_cyr($term)) {
          $term_transliterated = $this->cyr_to_lat($term);
        } else {
          $term_transliterated = $this->lat_to_cyr($term, false, (get_rstr_option('fix-diacritics', 'no')!='no'));
        }
      } else if($search_mode == 'plugin-mode') {
  			$term_transliterated = $this->transliterate_text(
  				(get_rstr_option('fix-diacritics', 'no') == 'yes' ? parent::fix_diacritics($term) : $term),
  				(get_rstr_option('site-script', 'lat') == 'cyr' ? 'lat_to_cyr' : 'cyr_to_lat')
  			);
      }

			// If there is an $exclusion_prefix, terms prefixed with it should be excluded.
			$exclude = $exclusion_prefix && ( substr( $term, 0, 1 ) === $exclusion_prefix );

			if ( $exclude ) {
				$like_op = 'NOT LIKE';
				$andor_op = 'AND';
				$term = substr( $term, 1 );
				$term_transliterated = substr( $term_transliterated, 1 );
			} else {
				$like_op  = 'LIKE';
				$andor_op = 'OR';
			}

			if ( $n && !$exclude ) {
				$like							= '%' . $wpdb->esc_like( $term ) . '%';
				$q['search_orderby_title'][]	= $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );

				$like							= '%' . $wpdb->esc_like( $term_transliterated ) . '%';
				$q['search_orderby_title'][]	= $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );
			}

			$like = $n . $wpdb->esc_like( $term ) . $n;
			$tr_like = $n . $wpdb->esc_like( $term_transliterated ) . $n;
			$search .= $wpdb->prepare("{$searchand}(
				(
					({$wpdb->posts}.post_title {$like_op} %s)
				{$andor_op}
					({$wpdb->posts}.post_excerpt {$like_op} %s)
				{$andor_op}
					({$wpdb->posts}.post_content {$like_op} %s)
				)
				OR
				(
					({$wpdb->posts}.post_title {$like_op} %s)
				{$andor_op}
					({$wpdb->posts}.post_excerpt {$like_op} %s)
				{$andor_op}
					({$wpdb->posts}.post_content {$like_op} %s)
				)
			)", $like, $like, $like, $tr_like, $tr_like, $tr_like);
			$searchand = ' AND ';
		}

		if (!empty($search)) {
			$search = " AND ({$search}) ";
			if (!is_user_logged_in())
				$search .= " AND ({$wpdb->posts}.post_password = '') ";
		}

		return $search;
	}

}
endif;
