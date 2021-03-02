<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * WP-CLI Helpers
 * @since     1.4.3
 * @verson    1.0.0
 */

if(class_exists('WP_CLI_Command') && !class_exists('Serbian_Transliteration_WP_CLI')):
	class Serbian_Transliteration_WP_CLI extends WP_CLI_Command {
		/**
		 * This tool can rename all existing Cyrillic permalinks to Latin inside database
		 *
		 * ## OPTIONS
		 *
		 * ## EXAMPLES
		 *
		 *     wp transliterate permalinks
		 *
		 * @when after_wp_load
		 */
		public function permalinks ( $args, $assoc_args ) {
			global $wpdb;
			
			$updated = 0;
			
			$get_post_types = get_post_types(array(
				'public'   => true
			), 'names', 'and');
			
			$post_type = join(', ', $get_post_types);
			$post_type_query = "FIND_IN_SET(`post_type`, '{$post_type}')";
			$get_results = $wpdb->get_results("SELECT `ID`, `post_name`, `post_title` FROM `{$wpdb->posts}` WHERE {$post_type_query} AND TRIM(IFNULL(`post_name`,'')) <> '' AND `post_type` NOT LIKE 'revision' AND `post_status` NOT LIKE 'trash' ORDER BY `ID` DESC");
			
			if($get_results)
			{
				$inst = Serbian_Transliteration::__instance();
				// Fix  problematic
				$get_results = array_map(function($match) use (&$wpdb, &$inst, &$updated){
					$match->post_name = $inst->decode( $match->post_name );
					$match->post_name = $inst->cyr_to_lat_sanitize( $match->post_name );
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
						WP_CLI::success( sprintf(
							__('Updated page ID %1$d, (%2$s) at URL: %3$s', RSTR_NAME),
							$match->ID,
							$match->post_title,
							get_the_permalink($match->ID)
						));
					}
				}, $get_results);
			}
			
			if($updated > 0){
				WP_CLI::success( sprintf(__('%d permalink changes were successfully made.', RSTR_NAME), $updated ));
			} else {
				WP_CLI::error( __('No changes to the permalink have been made.', RSTR_NAME), false );
			}
		}
	}
endif;



if( defined( 'WP_CLI' ) && WP_CLI) {
	WP_CLI::add_command( 'transliterate', 'Serbian_Transliteration_WP_CLI' );
}