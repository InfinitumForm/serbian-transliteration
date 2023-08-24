<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Transliteration tools
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @autor             Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Tools', false)) :
class Serbian_Transliteration_Tools extends Serbian_Transliteration
{
	public static function instance() {
		$class = self::class;
		$instance = Serbian_Transliteration_Cache::get($class);
		if ( !$instance ) {
			$instance = Serbian_Transliteration_Cache::set($class, new self());
		}
		return $instance;
	}
	
	function __construct() {
		$this->add_action( 'wp_ajax_rstr_run_permalink_transliteration', 'ajax__run_permalink_transliteration');
		$this->add_action( 'wp_ajax_rstr_transliteration_letters', 'ajax__rstr_transliteration_letters');
	}
	
	/*
	 * AJAX Transliterator
	**/
	public function ajax__rstr_transliteration_letters () {
		if(isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'rstr-transliteration-letters') !== false){
			$value = sanitize_textarea_field($_REQUEST['value']);
			$value = parent::fix_diacritics($value);
			echo esc_html( $this->transliterate_text($value, sanitize_text_field($_REQUEST['mode']), true) );
			exit;
		}
		
		echo __('An error occurred while converting. Please refresh the page and try again.', 'serbian-transliteration');
		exit;
	}
	
	/*
	 * AJAX update permalinks cyr to lat
	**/
	public function ajax__run_permalink_transliteration () {
		global $wpdb;
		
		$data = array(
			'error' => true,
			'done'   => false,
			'message' => __('There was a communication problem. Please refresh the page and try again. If this does not solve the problem, contact the author of the plugin.', 'serbian-transliteration'),
			'loading' => false
		);
		
		if(isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'rstr-run-permalink-transliteration') !== false)
		{
			// Posts per page
			$posts_pre_page = apply_filters('rstr/permalink-tool/transliteration/offset', 20);
			
			// Set post type
			if(isset($_REQUEST['post_type']) && !empty($_REQUEST['post_type'])) {
				if(is_array($_REQUEST['post_type'])) {
					$post_type = join(',', array_map(function($val){
						return sanitize_text_field($val);
					}, $_REQUEST['post_type']));
				} else {
					$post_type = sanitize_text_field($_REQUEST['post_type']);
				}
				$post_type_query = "FIND_IN_SET(`post_type`, '{$post_type}')";
			} else {
				$post_type = NULL;
				$post_type_query = 0;
			}
			
			// Get maximum number of the posts
			if(isset($_POST['total'])){
				$total = absint($_POST['total']);
			} else {
				$total = absint($wpdb->get_var("SELECT COUNT(1) FROM `{$wpdb->posts}` WHERE {$post_type_query} AND `post_type` NOT LIKE 'revision' AND TRIM(IFNULL(`post_name`,'')) <> '' AND `post_status` NOT LIKE 'trash'"));
			}
			
			// Get updated
			$updated = (isset($_POST['updated']) ? absint($_POST['updated']) : 0);
			
			// Get current page
			$paged = (isset($_POST['paged']) ? absint($_POST['paged'])+1 : 1);
			
			// Calculate offset
			$pages = ceil($total / $posts_pre_page);
				if($pages < 1) $pages = 1;
			
			// Percentage
			$percentage = round((($paged/$pages)*100),2);
			if($percentage > 100) $percentage = 100;
			if($percentage < 0) $percentage = 0;
			
			// Let's do the transliteration
			$return = array();
			if($total) {
				$offset = ($paged-1);
				if($offset<0) $offset = 0;
				$offset = ($posts_pre_page*$offset);
				
				$limit = $wpdb->prepare('LIMIT %d, %d', $offset, $posts_pre_page);
				$get_results = $wpdb->get_results("SELECT `ID`, `post_name` FROM `{$wpdb->posts}` WHERE {$post_type_query} AND TRIM(IFNULL(`post_name`,'')) <> '' AND `post_type` NOT LIKE 'revision' AND `post_status` NOT LIKE 'trash' ORDER BY `ID` DESC {$limit}");
				
				if($get_results)
				{
					// Fix  problematic
					$get_results = array_map(function($match) use (&$updated, &$wpdb, &$return){
						$match->post_name = Serbian_Transliteration_Utilities::decode( $match->post_name );
						$match->post_name = $this->cyr_to_lat_sanitize( $match->post_name );
						if($wpdb->update(
							$wpdb->posts,
							array(
								'post_name' => $this->cyr_to_lat_sanitize($match->post_name),
							),
							array(
								'ID' => $match->ID
							), array(
								'%s'
							), array(
								'%d'
							)
						)) {
							++$updated;
							$return[]=$match;
						}
						return $match;
					}, $get_results);
				}
			}
			
			if($paged<$pages)
			{
				$data = array(
					'error'   => false,
					'done'   => false,
					'message' => NULL,
					'posts_pre_page'   => $posts_pre_page,
					'paged'    => $paged,
					'total'   => $total,
					'pages'   => $pages,
					'loading' => true,
					'percentage' => $percentage,
					'updated' => $updated,
					'nonce' => $_REQUEST['nonce'],
					'action' => $_REQUEST['action'],
					'post_type' => $post_type
				);
			}
			else
			{
				$data = array(
					'error'   => false,
					'done'   => true,
					'message' => NULL,
					'loading' => true,
					'percentage' => $percentage,
					'return' => $return,
					'updated' => $updated,
					'nonce' => $_REQUEST['nonce'],
					'action' => $_REQUEST['action'],
					'post_type' => $post_type
				);
			}
		}
		
		header('Content-Type: application/json');
		exit(json_encode($data));
	}
}
endif;