<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * WP-CLI Helpers
 * @since     1.4.3
 * @verson    1.0.1
 * @author    Ivijan-Stefan Stipic
 */

if(class_exists('WP_CLI_Command', false) && !class_exists('Serbian_Transliteration_WP_CLI', false)):
	class Serbian_Transliteration_WP_CLI extends WP_CLI_Command {
		/**
		 * This tool can rename all existing Cyrillic permalinks to Latin inside database
		 *
		 * ## OPTIONS
		 *
		 *     --script=<lat|cyr>    (optional) Change transliteration type (Latin or Cyrillic)
		 *                                      If it is set to "cyr", then it will translate permalinks
		 *                                      from Latin to Cyrillic
		 *
		 * ## EXAMPLES
		 *
		 *     wp transliterate permalinks                  Translate permalinks from Cyrillic to Latin
		 *     wp transliterate permalinks --script=cyr     Translate permalinks from Latin to Cyrillic 
		 *
		 * @when after_wp_load
		 */
		public function permalinks ( $args, $assoc_args ) {
			global $wpdb;

			$updated = 0;
			
			$type = $assoc_args['script'] ?? 'lat';
			if('cyr' === $type) {
				$type = 'lat_to_cyr';
			} else {
				$type = 'cyr_to_lat';
			}

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
				WP_CLI::log( PHP_EOL.PHP_EOL );
				WP_CLI::log( __('Please wait! Do not close the terminal or terminate the script until this operation is completed!', 'serbian-transliteration') );
				$progress = \WP_CLI\Utils\make_progress_bar( __('Progress:', 'serbian-transliteration'), count($get_results) );
				$get_results = array_map(function($match) use (&$wpdb, &$inst, &$updated, &$type, &$progress){
					$progress->tick();
					
					$old_post_name = $match->post_name;
					
					$match->post_name = Serbian_Transliteration_Utilities::decode( $match->post_name );
					if('lat_to_cyr' === $type) {
						$match->post_name = $inst->lat_to_cyr( $match->post_name, false, false );
					} else {
						$match->post_name = $inst->cyr_to_lat_sanitize( $match->post_name );
					}
					
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
						
						delete_post_meta($match->ID, '_wp_old_slug');
						
						if('lat_to_cyr' === $type) {
							update_post_meta($match->ID, '_wp_cyr_slug', $inst->lat_to_cyr($match->post_name));
							update_post_meta($match->ID, '_wp_lat_slug', $inst->cyr_to_lat_sanitize($old_post_name));
						} else {
							update_post_meta($match->ID, '_wp_cyr_slug', $inst->lat_to_cyr($old_post_name));
							update_post_meta($match->ID, '_wp_lat_slug', $inst->cyr_to_lat_sanitize($match->post_name));
						}
						++$updated;
						WP_CLI::success( sprintf(
							__('Updated page ID %1$d, (%2$s) at URL: %3$s', 'serbian-transliteration'),
							$match->ID,
							$match->post_title,
							get_the_permalink($match->ID)
						));
					}
				}, $get_results);
				
				$progress->finish();
				WP_CLI::log( PHP_EOL.PHP_EOL );
			}

			if($updated > 0){
				WP_CLI::success( sprintf(_n('%d permalink was successfully transliterated.', '%d permalinks were successfully transliterated.', $updated, 'serbian-transliteration'), $updated ));
			} else {
				WP_CLI::error( __('No changes to the permalink have been made.', 'serbian-transliteration'), false );
			}
		}
	}
endif;

// Add comands
if( defined( 'WP_CLI' ) && WP_CLI) {
	WP_CLI::add_command( 'transliterate', 'Serbian_Transliteration_WP_CLI' );
}
