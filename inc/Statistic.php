<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * WP-CLI Helpers
 * @since     1.4.3
 * @verson    1.0.0
 */

if(!class_exists('Serbian_Transliteration_Statistic')):
	class Serbian_Transliteration_Statistic {
		
		// API call
		protected static $url = 'http://159.203.47.151/plugin_stat/index.php';
		
		// Send data on activation
		public static function activation ($data = '') {
			return wp_remote_get(self::$url.'?'.http_build_query(array(
				'plugin_name' => RSTR_NAME,
				'domain' => home_url('/'),
				'plugin_id' => get_option(RSTR_NAME . '-ID'),
				'plugin_version' => RSTR_VERSION,
				'data' => (!empty($data) ? json_encode($data) : ''),
				'action' => 'activation'
			)));
		}
		// Send data on deactivation
		public static function deactivation () {
			return wp_remote_get(self::$url.'?'.http_build_query(array(
				'plugin_name' => RSTR_NAME,
				'domain' => home_url('/'),
				'plugin_id' => get_option(RSTR_NAME . '-ID'),
				'plugin_version' => RSTR_VERSION,
				'action' => 'deactivation'
			)));
		}
		// Send data on uninstall
		public static function uninstall () {
			return wp_remote_get(self::$url.'?'.http_build_query(array(
				'plugin_name' => RSTR_NAME,
				'domain' => home_url('/'),
				'plugin_id' => get_option(RSTR_NAME . '-ID'),
				'action' => 'uninstall'
			)));
		}
	}
endif;