<?php if ( !defined('WPINC') ) die();
/**
 * Notices
 *
 * @link              http://infinitumform.com/
 * @since             1.4.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Transliteration_Notifications', false)) :
	class Transliteration_Notifications extends Transliteration
	{		
		public function __construct() {
			if(!is_admin()) {
				return;
			}
			
			$this->add_action( 'admin_init', 'check_installation_time' );
			$this->add_action( 'admin_init', 'rstr_dimiss_review', 5 );
			$this->add_action( 'admin_init', 'rstr_dimiss_donation', 5 );
		}
		
		// remove the notice for the user if review already done or if the user does not want to
		public function rstr_dimiss_review(){
			if( isset( $_GET['rstr_dimiss_review'] ) && !empty( $_GET['rstr_dimiss_review'] ) ){
				$rstr_dimiss_review = $_GET['rstr_dimiss_review'];
				if( $rstr_dimiss_review == 1 ){
					add_option( 'serbian-transliteration-reviewed' , time() );
					
					$parse_url = Transliteration_Utilities::parse_url();
					if(wp_safe_redirect(remove_query_arg('rstr_dimiss_review', $parse_url['url']))) {
						exit;
					}
				}
			}
		}
		
		// remove the notice for the user if donation already done or if the user does not want to
		public function rstr_dimiss_donation(){
			if( isset( $_GET['rstr_dimiss_donation'] ) && !empty( $_GET['rstr_dimiss_donation'] ) ){
				$rstr_dimiss_donation = $_GET['rstr_dimiss_donation'];
				if( $rstr_dimiss_donation == 1 ){
					add_option( 'serbian-transliteration-donated' , time() );
					
					$parse_url = Transliteration_Utilities::parse_url();
					if(wp_safe_redirect(remove_query_arg('rstr_dimiss_donation', $parse_url['url']))) {
						exit;
					}
				}
			}
		}
		
		// check if review notice should be shown or not
		public function check_installation_time() {
			$this->display_vote();
			$this->display_donation();
		}
		
		// dimiss vote notices
		public function display_vote() {
			if(get_option('serbian-transliteration-reviewed')){
				return;
			}
			
			$get_dates = get_option( 'serbian-transliteration-activation' );
			if(is_array($get_dates)){
				$install_date = strtotime(end($get_dates));
			} else {
				$install_date = strtotime($get_dates);
			}
			
			$past_date = strtotime( '-5 days' );
		 
			if ( $past_date >= $install_date ) {
				$this->add_action( 'admin_notices', 'notice__give_us_vote', 1 );
			}
		}
		
		// dimiss vote notices
		public function display_donation() {
			if( !get_option('serbian-transliteration-reviewed') ) {
				return;
			}
			
			if(get_option('serbian-transliteration-donated')){
				return;
			}
			
			$get_dates = get_option( 'serbian-transliteration-activation' );
			if(is_array($get_dates)){
				$install_date = strtotime(end($get_dates));
			} else {
				$install_date = strtotime($get_dates);
			}
			
			$past_date = strtotime( '-3 weeks' );

			if ( $past_date >= $install_date ) {
				$this->add_action( 'admin_notices', 'notice__buy_me_a_coffee', 1 );
			}
		}
		
		/**
		 * Display Admin Notice, asking for a review
		**/
		public function notice__give_us_vote() {
			$parse_url = Transliteration_Utilities::parse_url();
			$dont_disturb = esc_url( add_query_arg('rstr_dimiss_review', '1', $parse_url['url']) );
			$plugin_info = get_plugin_data( RSTR_FILE , true, true );       
			$reviewurl = esc_url( 'https://wordpress.org/support/plugin/serbian-transliteration/reviews/?filter=5#new-post' );
		 
			printf(
				'<div class="notice notice-info"><h3>'.__('You have been using <b> %1$s </b> plugin for a while. We hope you liked it!', 'serbian-transliteration').'</h3><p>'.__('Please give us a quick rating, it works as a boost for us to keep working on the plugin!', 'serbian-transliteration').'</p><p class="void-review-btn"><a href="%2$s" class="button button-primary" target="_blank">'.__('Rate Now!', 'serbian-transliteration').'</a><a href="%3$s" class="void-grid-review-done" style="margin-left: 10px;">'.__('I\'ve already done that!', 'serbian-transliteration').'</a></p></div>',
				$plugin_info['Name'],
				$reviewurl,
				$dont_disturb
			);
		}
		
		
		/**
		 * Display Admin Notice, asking for a review
		**/
		public function notice__buy_me_a_coffee() {
			$parse_url = Transliteration_Utilities::parse_url();
			$dont_disturb = esc_url( add_query_arg('rstr_dimiss_donation', '1', $parse_url['url']) );
			$plugin_info = get_plugin_data( RSTR_FILE , true, true );       
			$donationurl = 'https://www.buymeacoffee.com/ivijanstefan';

			echo '<div class="notice notice-info">
				<h3>'. sprintf(
					__('Hey there! It\'s been a while since you\'ve been using the <b> %1$s </b> plugin', 'serbian-transliteration'),
					$plugin_info['Name']
				).'</h3>
				
				<p>'.sprintf(
					__('I\'m glad to hear you\'re enjoying the plugin. I\'ve put a lot of time and effort into ensuring that your website runs smoothly. If you\'re feeling generous, how about %s for my hard work? 😊', 'serbian-transliteration'),
					
					'<big><strong><a href="'.esc_url($donationurl).'" target="_blank">'.__('treating me to a coffee', 'serbian-transliteration').'</a></strong></big>'
				).'</p>
				<p>'.sprintf(
					__('Or simply %s forever.', 'serbian-transliteration'),
					'<a href="'.esc_url($dont_disturb).'">'.__('hide this message', 'serbian-transliteration').'</a>'
				).'</p>
			</div>';
		}

	}
endif;