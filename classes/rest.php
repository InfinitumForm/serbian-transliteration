<?php if ( !defined('WPINC') ) die();

class Transliteration_Rest extends Transliteration {
    
    public function __construct() {
		$this->add_action('plugins_loaded', 'register_rest_transliteration');
    }
	
	public function register_rest_transliteration() {
		$this->add_action('rest_pre_echo_response', 'rest_response');
	}
	
	public function rest_response($response) {
		return Transliteration_Mode::get()->transliterate_objects($response);
	}
	
}