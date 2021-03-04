<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Notices
 *
 * @link              http://infinitumform.com/
 * @since             1.4.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Notice')) :
	class Serbian_Transliteration_Notice extends Serbian_Transliteration
	{
		/* Run this script */
		public static function init() {
			global $rstr_cache;
			if ( !$rstr_cache->get('Serbian_Transliteration_Notice') ) {
				$rstr_cache->set('Serbian_Transliteration_Notice', new self());
			}
			return $rstr_cache->get('Serbian_Transliteration_Notice');
		}
		
		public function __construct() {
			$this->add_action( 'admin_init', 'check_installation_time' );
			$this->add_action( 'admin_init', 'spare_me', 5 );
		}
		
		// remove the notice for the user if review already done or if the user does not want to
		function spare_me(){    
			if( isset( $_GET['spare_me'] ) && !empty( $_GET['spare_me'] ) ){
				$spare_me = $_GET['spare_me'];
				if( $spare_me == 1 ){
					add_option( RSTR_NAME . '-no-reviews' , true );
				}
			}
		}
		
		// check if review notice should be shown or not
		public function check_installation_time() {
			
			if(get_option(RSTR_NAME . '-no-reviews')){
				return;
			}
			
			$get_dates = get_option( RSTR_NAME. '-activation' );
			if(is_array($get_dates)){
				$install_date = strtotime(end($get_dates));
			} else {
				$install_date = strtotime($get_dates);
			}
			
			$past_date = strtotime( '-7 days' );
		 
			if ( $past_date >= $install_date) {
				$this->add_action( 'admin_notices', 'display_admin_notice' );
			}
		}
		
		/**
		 * Display Admin Notice, asking for a review
		**/
		function display_admin_notice() {
			$parse_url = $this->parse_url();
			$dont_disturb = esc_url( add_query_arg('spare_me', '1', $parse_url['url']) );
			$plugin_info = get_plugin_data( RSTR_FILE , true, true );       
			$reviewurl = esc_url( 'https://wordpress.org/support/plugin/serbian-transliteration/reviews/?filter=5#new-post' );
		 
			printf(__('<div class="notice notice-info"><h3>'.__('You have been using <b> %s </b> for a while. We hope you liked it!', RSTR_NAME).'</h3><p>'.__('Please give us a quick rating, it works as a boost for us to keep working on the plugin!', RSTR_NAME).'</p><p class="void-review-btn"><a href="%s" class="button button-primary" target="_blank">'.__('Rate Now!', RSTR_NAME).'</a><a href="%s" class="void-grid-review-done">'.__('I\'ve already done that!', RSTR_NAME).'</a></p></div>', $plugin_info['TextDomain']), $plugin_info['Name'], $reviewurl, $dont_disturb );
		}

	}
endif;