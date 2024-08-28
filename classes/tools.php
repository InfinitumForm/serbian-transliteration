<?php if ( !defined('WPINC') ) die();

class Transliteration_Tools extends Transliteration {
	public function __construct() {
		if( !is_admin() ) return;
		
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
	public function permalink_transliteration() {
		global $wpdb;

		$data = array(
			'error' => true,
			'done' => false,
			'message' => __('There was a communication problem. Please refresh the page and try again. If this does not solve the problem, contact the author of the plugin.', 'serbian-transliteration'),
			'loading' => false
		);

		if (isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'rstr-run-permalink-transliteration') !== false) {
			// Posts per page
			$posts_per_page = apply_filters('transliteration_permalink_transliteration_batch_size', 50);
			$posts_per_page = apply_filters_deprecated('rstr/permalink-tool/transliteration/offset', [$posts_per_page], '2.0.0', 'transliteration_permalink_transliteration_batch_size');

			// Set post type
			$post_type = isset($_REQUEST['post_type']) ? (is_array($_REQUEST['post_type']) ? implode(',', array_map('sanitize_text_field', $_REQUEST['post_type'])) : sanitize_text_field($_REQUEST['post_type'])) : null;
			$post_type_query = $post_type ? "FIND_IN_SET(`post_type`, '{$post_type}')" : "1=1"; // Use "1=1" as a fallback to avoid SQL syntax errors

			// Get maximum number of the posts
			$total = isset($_POST['total']) ? absint($_POST['total']) : absint($wpdb->get_var("SELECT COUNT(1) FROM `{$wpdb->posts}` WHERE {$post_type_query} AND `post_type` NOT LIKE 'revision' AND TRIM(IFNULL(`post_name`,'')) <> '' AND `post_status` NOT LIKE 'trash'"));

			// Get updated and current page
			$updated = isset($_POST['updated']) ? absint($_POST['updated']) : 0;
			$paged = isset($_POST['paged']) ? absint($_POST['paged']) + 1 : 1;

			// Calculate pagination values
			$pages = max(ceil($total / $posts_per_page), 1);
			$percentage = min(max(round(($paged / $pages) * 100, 2), 0), 100);

			// Perform transliteration
			$return = array();
			if ($total) {
				$offset = ($paged - 1) * $posts_per_page;
				$get_results = $wpdb->get_results($wpdb->prepare("SELECT `ID`, `post_name` FROM `{$wpdb->posts}` WHERE {$post_type_query} AND TRIM(IFNULL(`post_name`,'')) <> '' AND `post_type` NOT LIKE 'revision' AND `post_status` NOT LIKE 'trash' ORDER BY `ID` DESC LIMIT %d, %d", $offset, $posts_per_page));

				if ($get_results) {
					foreach ($get_results as $match) {
						$original_post_name = $match->post_name;
						$match->post_name = Transliteration_Utilities::decode($match->post_name);
						$match->post_name = Transliteration_Controller::get()->cyr_to_lat_sanitize($match->post_name);

						if ($match->post_name !== $original_post_name && wp_update_post(array('ID' => $match->ID, 'post_name' => $match->post_name))) {
							$updated++;
							$return[] = $match;
						}
					}
				}
			}

			if ($percentage >= 100 && function_exists('flush_rewrite_rules')) {
				flush_rewrite_rules();
			}

			if ($paged < $pages) {
				$data = array(
					'error' => false,
					'done' => false,
					'message' => null,
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'total' => $total,
					'pages' => $pages,
					'loading' => true,
					'percentage' => $percentage,
					'updated' => $updated,
					'nonce' => sanitize_text_field($_REQUEST['nonce']),
					'action' => sanitize_text_field($_REQUEST['action']),
					'post_type' => $post_type
				);
			} else {
				$data = array(
					'error' => false,
					'done' => true,
					'message' => null,
					'loading' => true,
					'percentage' => $percentage,
					'return' => $return,
					'updated' => $updated,
					'nonce' => sanitize_text_field($_REQUEST['nonce']),
					'action' => sanitize_text_field($_REQUEST['action']),
					'post_type' => $post_type
				);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}
}