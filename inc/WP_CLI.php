<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }

use WP_CLI_Command;

class Serbian_Transliteration_WP_CLI extends WP_CLI_Command {
	/**
	* This tool can rename all existing Cyrillic permalinks to Latin inside database
	* ## OPTIONS
	*
	*/
	public function permalink ( $options=array() ) {
		global $wpdb;
		
		$data = array(
			'error' => true,
			'done'   => false,
			'message' => 'There was a communication problem. Please refresh the page and try again. If this does not solve the problem, contact the author of the plugin.',
			'loading' => false
		);
		
		// Get post types
		if(!isset($options['post_type'])){
			$get_post_types = get_post_types(array(
				'public'   => true
			), 'names', 'and');

			$options['post_type'] = join(',', $get_post_types);
		}
		
		// Posts per page
		$posts_pre_page = apply_filters('rstr/permalink-tool/transliteration/offset', (isset($options['posts_pre_page']) ? absint($options['posts_pre_page']) : 20));
		
		// Set post type
		if(isset($options['post_type']) && !empty($options['post_type'])) {
			if(is_array($options['post_type'])) {
				$post_type = join(',', array_map(function($val){
					return sanitize_text_field($val);
				}, $options['post_type']));
			} else {
				$post_type = sanitize_text_field($options['post_type']);
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
					$match->post_name = Serbian_Transliteration::__instance()->decode( $match->post_name );
					$match->post_name = Serbian_Transliteration::__instance()->cyr_to_lat_sanitize( $match->post_name );
					if($wpdb->update(
						$wpdb->posts,
						array(
							'post_name' => Serbian_Transliteration::__instance()->cyr_to_lat_sanitize($match->post_name),
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
				'message' => 'Please wait! Do not close the window or leave the page until this operation is completed!',
				'posts_pre_page'   => $posts_pre_page,
				'paged'    => $paged,
				'total'   => $total,
				'pages'   => $pages,
				'loading' => true,
				'percentage' => $percentage,
				'updated' => $updated,
				'nonce' => $options['nonce'],
				'action' => $options['action'],
				'post_type' => $post_type
			);
			
			WP_CLI::line( $data['message'] );
			
			return $this->permalink($data);
		}
		else
		{
			$data = array(
				'error'   => false,
				'done'   => true,
				'message' => 'DONE!!!',
				'loading' => true,
				'percentage' => $percentage,
				'return' => $return,
				'updated' => $updated,
				'nonce' => $options['nonce'],
				'action' => $options['action'],
				'post_type' => $post_type
			);
			
			WP_CLI::success( $data['message'] );
		}
	}
}

WP_CLI::add_command( 'transliterate', 'Serbian_Transliteration_WP_CLI' );