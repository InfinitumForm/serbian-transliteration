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
			$class = get_called_class();
			if(!$class){
				$class = self::class;
			}
			$instance = $rstr_cache->get($class);
			if ( !$instance ) {
				$instance = $rstr_cache->set($class, new self());
			}
			return $instance;
		}
		
		public function __construct() {
			$this->add_action( 'admin_init', 'check_installation_time' );
			$this->add_action( 'admin_init', 'cfgp_dimiss_review', 5 );
		}
		
		// remove the notice for the user if review already done or if the user does not want to
		public function cfgp_dimiss_review(){    
			if( isset( $_GET['cfgp_dimiss_review'] ) && !empty( $_GET['cfgp_dimiss_review'] ) ){
				$cfgp_dimiss_review = $_GET['cfgp_dimiss_review'];
				if( $cfgp_dimiss_review == 1 ){
					add_option( RSTR_NAME . '-reviewed' , true );
					
					$parse_url = Serbian_Transliteration_Utilities::parse_url();
					if(wp_safe_redirect(remove_query_arg('cfgp_dimiss_review', $parse_url['url']))) {
						exit;
					}
				}
			}
		}
		
		// check if review notice should be shown or not
		public function check_installation_time() {
			
			if(get_option(RSTR_NAME . '-reviewed')){
				return;
			}
			
			$get_dates = get_option( RSTR_NAME. '-activation' );
			if(is_array($get_dates)){
				$install_date = strtotime(end($get_dates));
			} else {
				$install_date = strtotime($get_dates);
			}
			
			$past_date = strtotime( '-5 days' );
		 
			if ( $past_date >= $install_date) {
				$this->add_action( 'admin_notices', 'display_admin_notice' );
			}
		}
		
		/**
		 * Display Admin Notice, asking for a review
		**/
		public function display_admin_notice() {
			$parse_url = Serbian_Transliteration_Utilities::parse_url();
			$dont_disturb = esc_url( add_query_arg('cfgp_dimiss_review', '1', $parse_url['url']) );
			$plugin_info = get_plugin_data( RSTR_FILE , true, true );       
			$reviewurl = esc_url( 'https://wordpress.org/support/plugin/serbian-transliteration/reviews/?filter=5#new-post' );
		 
			printf(
				'<div class="notice notice-info"><h3>'.__('You have been using <b> %1$s </b> plugin for a while. We hope you liked it!', RSTR_NAME).'</h3><p>'.__('Please give us a quick rating, it works as a boost for us to keep working on the plugin!', RSTR_NAME).'</p><p class="void-review-btn"><a href="%2$s" class="button button-primary" target="_blank">'.__('Rate Now!', RSTR_NAME).'</a><a href="%3$s" class="void-grid-review-done">'.__('I\'ve already done that!', RSTR_NAME).'</a></p></div>',
				$plugin_info['Name'],
				$reviewurl,
				$dont_disturb
			);
		}

	}
endif;