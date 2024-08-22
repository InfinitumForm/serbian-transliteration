<?php if ( !defined('WPINC') ) die();

class Transliteration_Tools extends Transliteration {
	public function __construct() {
		$this->add_action( 'wp_ajax_rstr_transliteration_letters', 'transliteration_letters');
		$this->add_action( 'wp_ajax_rstr_run_permalink_transliteration', 'permalink_transliteration');
	}
	
	/*
	 * AJAX Transliterator
	 */
	public function transliteration_letters() {
		if (!isset($_REQUEST['nonce']) || wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'rstr-transliteration-letters') === false) {
			echo __('An error occurred while converting. Please refresh the page and try again.', 'serbian-transliteration');
			exit;
		}

		$value = sanitize_textarea_field($_REQUEST['value']);
		if (empty($value)) {
			echo __('The field is empty.', 'serbian-transliteration');
			exit;
		}

		$mode = sanitize_text_field($_REQUEST['mode']);
		$transliterationController = Transliteration_Controller::get();

		if ($mode === 'lat_to_cyr') {
			$result = $transliterationController->lat_to_cyr($value, false, true);
		} else {
			$result = $transliterationController->cyr_to_lat($value);
		}

		echo esc_html(html_entity_decode($result, ENT_QUOTES, 'UTF-8'));
		exit;
	}
	
	/*
	 * AJAX update permalinks cyr to lat
	 */
	public function permalink_transliteration () {
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
						$match->post_name = Transliteration_Utilities::decode( $match->post_name );
						$match->post_name = Transliteration_Controller::get()->cyr_to_lat_sanitize($match->post_name);
						if($wpdb->update(
							$wpdb->posts,
							array(
								'post_name' => $match->post_name,
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