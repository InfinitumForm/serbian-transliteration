<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Rest', false) ) : class Transliteration_Rest extends Transliteration {
    
    public function __construct() {
		if(
			get_rstr_option('transliteration-mode', 'cyr_to_lat') !== 'none' 
			&& get_rstr_option('force-rest-api', 'yes') == 'yes' 
			&& !Transliteration_Utilities::skip_transliteration() 
			&& !Transliteration_Controller::get()->disable_transliteration()
			&& !is_null(Transliteration_Map::get()->map())
		) {
			$this->add_action('plugins_loaded', 'register_rest_transliteration');
		}
    }
	
	public function register_rest_transliteration() {
		$this->add_action('rest_pre_echo_response', 'rest_response');
	}
	
	public function rest_response($response) {
		return Transliteration_Mode::get()->transliterate_objects($response);
	}
	
} endif;