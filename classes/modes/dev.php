<?php if ( !defined('WPINC') ) die();

class Transliteration_Mode_Dev {
	use Transliteration__Cache;
    
	// Mode ID
	const MODE = 'dev';
	
    /*
	 * The main constructor
	 */
    public function __construct() {
		
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
		$filters = [
			'gettext' 				=> 'gettext_content',
			'ngettext' 				=> 'content',
		];

		if (!current_theme_supports( 'title-tag' )){
			unset($filters['document_title_parts']);
			unset($filters['pre_get_document_title']);
		} else {
			unset($filters['wp_title']);
		}

		return $filters;
	}
    
}