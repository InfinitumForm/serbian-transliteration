<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Email', false) ) : class Transliteration_Email extends Transliteration {
    
    public function __construct() {
		if( get_rstr_option('force-email-transliteration', 'no') == 'yes' ) {
			$this->add_action('phpmailer_init', 'transliterate_phpmailer', PHP_INT_MAX - 99, 1);
		}
    }
	
	public function transliterate_phpmailer($phpmailer) {
		$phpmailer->Body = Transliteration_Controller::get()->transliterate($phpmailer->Body??'');
		$phpmailer->Subject = Transliteration_Controller::get()->transliterate($phpmailer->Subject??'');
	}
	
} endif;