<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Wordpress', false) ) : class Transliteration_Wordpress extends Transliteration {
    
    public function __construct() {
		$this->add_filter('sanitize_user', 'allow_cyrillic_usernames', 10, 3);
		$this->add_filter('body_class', 'add_body_class', 10, 1);
    }
	
	public function allow_cyrillic_usernames($username, $raw_username, $strict) {
		if(get_rstr_option('allow-cyrillic-usernames', 'no') == 'no') {
			return;
		}
		
		$username = wp_strip_all_tags( $raw_username ?? '' );
		$username = remove_accents( $username );

		// Kill octets
		$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
		$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

		// If strict, reduce to ASCII and Cyrillic characters for max portability.
		if ( $strict ){
			$username = preg_replace( '|[^a-zа-я0-9 _.\-@]|iu', '', $username );
		}
		$username = trim( $username );

		// Consolidate contiguous whitespace
		$username = preg_replace( '|\s+|', ' ', $username );

		return $username;
	}
	
	public function add_body_class($classes){
		if(get_rstr_option('enable-body-class', 'no') == 'no') {
			return;
		}
		$script = Transliteration_Utilities::get_current_script();
		//body class based on the current script - cyr, lat
		$classes[] = 'rstr-' . $script;
		$classes[] = 'transliteration-' . $script;
		$classes[] = $script;
		return $classes;
	}
	
} endif;