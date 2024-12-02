<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Forced extends Transliteration {
	use Transliteration__Cache;
    
	// Mode ID
	const MODE = 'forced';
	
    /*
	 * The main constructor
	 */
    public function __construct() {
    }
	
	/*
	 * Initialize actions (to be called only once)
	 */
	public function init_actions() {
		$this->add_action('template_redirect', 'buffer_start', 1);
		$this->add_action('wp_footer', 'buffer_end', 100);
	}
	
	/*
	 * Get current instance
	 */
	public static function get() {
		return self::cached_static('instance', function(){
			return new self();
		});
	}
	
	/*
	 * Get available filters for this mode
	 */
	public function filters() {
		$filters = [];
		return $filters;
	}
	
	public function buffer_start() {
		$this->ob_start('buffer_callback');
	}
	
	public function buffer_callback( $buffer ) {
		return Transliteration_Controller::get()->transliterate($buffer);
	}

	public function buffer_end() {
		if (ob_get_level() > 0) {
			ob_end_flush();
		}
	}
    
}