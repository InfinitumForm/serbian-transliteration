<?php if ( !defined('WPINC') ) die();
/**
 * Notices
 *
 * @link              http://infinitumform.com/
 * @since             1.4.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Notifications extends Transliteration
{		
	public function __construct() {
		if(!is_admin()) {
			return;
		}
		
		$this->add_action( 'admin_init', 'check_installation_time' );
		$this->add_action( 'admin_init', 'rstr_dimiss_review', 5 );
		$this->add_action( 'admin_init', 'rstr_dimiss_donation', 5 );
		$this->add_action( 'admin_init', 'rstr_dimiss_ads', 5 );
	}
	
	// remove the notice for the user if review already done or if the user does not want to
	public function rstr_dimiss_review(){
		if( isset( $_GET['rstr_dimiss_review'] ) && !empty( $_GET['rstr_dimiss_review'] ) ){
			$rstr_dimiss_review = $_GET['rstr_dimiss_review'];
			if( $rstr_dimiss_review == 1 ){
				add_option( 'serbian-transliteration-reviewed' , time() );
				
				$parse_url = Transliteration_Utilities::parse_url();
				if(!headers_sent()) {
					if(wp_safe_redirect(remove_query_arg('rstr_dimiss_review', $parse_url['url']))) {
						exit;
					}
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
				if(!headers_sent()) {
					if(wp_safe_redirect(remove_query_arg('rstr_dimiss_donation', $parse_url['url']), 302)) {
						exit;
					}
				}
			}
		}
	}
	
	// remove ads notice
	public function rstr_dimiss_ads(){
		if( isset( $_GET['rstr_dimiss_adds'] ) && !empty( $_GET['rstr_dimiss_adds'] ) ){
			$rstr_dimiss_donation = $_GET['rstr_dimiss_adds'];
			if( $rstr_dimiss_donation == 1 ){
				set_transient( 'serbian-transliteration-ads', time(), MONTH_IN_SECONDS );
				
				$parse_url = Transliteration_Utilities::parse_url();
				if(!headers_sent()) {
					if(wp_safe_redirect(remove_query_arg('rstr_dimiss_adds', $parse_url['url']), 302)) {
						exit;
					}
				}
			}
		}
	}
	
	// check if review notice should be shown or not
	public function check_installation_time() {
		$this->display_vote();
		$this->display_donation();
		$this->display_ads();
	}
	
	// dimiss vote notices
	public function display_vote() {
		if(get_option('serbian-transliteration-reviewed')){
			return;
		}
		
		$get_dates = get_option( 'serbian-transliteration-activation' );
		
		if(is_array($get_dates)){
			$install_date = strtotime(reset($get_dates));
		} else {
			$install_date = strtotime($get_dates);
		}
		
		$past_date = strtotime( '-1 week' );
	 
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
			$install_date = strtotime(reset($get_dates));
		} else {
			$install_date = strtotime($get_dates);
		}
		
		$past_date = strtotime( '-3 weeks' );

		if ( $past_date >= $install_date ) {
			$this->add_action( 'admin_notices', 'notice__buy_me_a_coffee', 1 );
		}
	}
	
	// dimiss vote notices
	public function display_ads() {
		
		if( strpos(home_url('/'), 'freelanceposlovi.com') !== false ) {
			return;
		}
		
		if(get_transient('serbian-transliteration-ads')){
			return;
		}
		
		$get_dates = get_option( 'serbian-transliteration-activation' );
		
		if(is_array($get_dates)){
			$install_date = strtotime(reset($get_dates));
		} else {
			$install_date = strtotime($get_dates);
		}
		
		$past_date = strtotime( '-4 weeks' );

		if ( $past_date >= $install_date ) {
			$this->add_action( 'admin_notices', 'notice__ads', 1 );
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
	
	/**
	 * Display Admin Notice, asking for a review
	**/
	public function notice__ads() {
		$parse_url = Transliteration_Utilities::parse_url();
		$dont_disturb = add_query_arg('rstr_dimiss_adds', '1', $parse_url['url']);
	 
		printf(
			'<div class="notice notice-info is-dismissible" id="ads-freelance-poslovi"><img src="'.esc_url(RSTR_ASSETS.'/img/fp-icon-80x80.png').'" alt="FreelancePoslovi.com"><h3>'.__('Find Work or %1$s in Serbia, Bosnia, Croatia, and Beyond!', 'serbian-transliteration').'</h3><p>'.__('Visit %2$s to connect with skilled professionals across the region. Whether you need a project completed or are looking for work, our platform is your gateway to successful collaboration.', 'serbian-transliteration').'</p><p>%3$s</p><a href="%4$s" class="notice-dismiss" style="text-decoration:none;"></a></div>',
			'<a href="https://freelanceposlovi.com/" target="_blank" title="Freelance Poslovi">'.__('Hire Top Freelancers', 'serbian-transliteration').'</a>',
			'<a href="https://freelanceposlovi.com/" target="_blank" title="Freelance Poslovi"><b>'.__('Freelance Jobs', 'serbian-transliteration').'</b></a>',
			'<a href="https://freelanceposlovi.com/" target="_blank" class="button button-primary"><b>'.__('Join us today!', 'serbian-transliteration').'</b></a>',
			esc_url( $dont_disturb )
		);
		
		add_action('admin_footer', function(){ ?>
<style>/* <![CDATA[ */#ads-freelance-poslovi{border-left-color:#07bab9}#ads-freelance-poslovi>img{width:80px;height:80px;float:left;margin:24px 10px 24px 0}#ads-freelance-poslovi a{color:#07bab9;text-decoration:none}#ads-freelance-poslovi a:hover{color:#203b4e}#ads-freelance-poslovi .button-primary{background-color:#07bab9;border-color:#07bab9;color:#fff}#ads-freelance-poslovi .button-primary:hover{background-color:#203b4e;border-color:#203b4e;color:#fff}@media all and (max-width:1440px){#ads-freelance-poslovi>img{margin:30px 10px 30px 0}}@media all and (max-width:768px){#ads-freelance-poslovi>img{width:64px;height:64px;margin:15px 0 8px}}/* ]]> */</style>
		<?php });
	}

}