<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * Plugin Statistic
 *
 * This sends only basic plugin statistic information for the developers.
 * When you uninstall the plugin, this data is deleted from our database.
 * We only collect plugin ID, URL, version and plugin settings.
 * That's all we need.
 *
 * If you have any concerns, please contact us: infinitumform@gmail.com
 *
 * @since     1.6.4
 * @verson    1.0.0
 */

if(!class_exists('Serbian_Transliteration_Statistic')):
	class Serbian_Transliteration_Statistic {
		
		// API call
		protected static $url = 'https://cdn-cfgeoplugin.com/plugin_stat/index.php';
		
		// Send data on activation
		public static function activation ($data = '') {
			return self::remote_request(self::$url, array(
				'plugin_name' => RSTR_NAME,
				'domain' => home_url('/'),
				'plugin_id' => get_option(RSTR_NAME . '-ID'),
				'plugin_version' => RSTR_VERSION,
				'data' => (!empty($data) ? json_encode($data) : ''),
				'action' => 'activation'
			));
		}
		
		// Send data on deactivation
		public static function deactivation () {
			return self::remote_request(self::$url, array(
				'plugin_name' => RSTR_NAME,
				'domain' => home_url('/'),
				'plugin_id' => get_option(RSTR_NAME . '-ID'),
				'plugin_version' => RSTR_VERSION,
				'action' => 'deactivation'
			));
		}
		
		// Send data on uninstall
		public static function uninstall () {
			return self::remote_request(self::$url, array(
				'plugin_name' => RSTR_NAME,
				'domain' => home_url('/'),
				'plugin_id' => get_option(RSTR_NAME . '-ID'),
				'action' => 'uninstall'
			));
		}
		
		// PRIVATE: Request
		protected static function remote_request ($url, $data=NULL, $method = 'GET') {
			// cURL method
			if(function_exists('curl_init'))
			{
				// Get method
				if($method == 'GET') {
					if(!empty($data) && is_array($data)) {
						$data = http_build_query($data);
						$url = $url . ( (strpos($url, '?') !== false) ? '&' : '?' ) . $data;
					}
				}
				// Initialize cURL
				$curl = curl_init();
					// Set URL
					curl_setopt($curl, CURLOPT_URL, $url);
					// Send POST data
					if($method == 'POST') {
						curl_setopt($curl, CURLOPT_POST, 1);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					}
					// Setup
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
					curl_setopt($curl, CURLOPT_TIMEOUT, 3);
					// Accept
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
					// Return data
					$output=curl_exec($curl);
				curl_close($curl);
			} else {
				// WP remote
				if($method == 'POST') {
					$output = wp_remote_post(
						$url,
						array(
							'body' => $data,
							'headers' => array(
								'Accept' => 'application/json'
							)
						)
					);
				} else {
					
					if(!empty($data) && is_array($data)) {
						$data = http_build_query($data);
						$url = $url . ( (strpos($url, '?') !== false) ? '&' : '?' ) . $data;
					}
					
					$output = wp_remote_get(
						$url,
						array(
							'headers' => array(
								'Accept' => 'application/json'
							)
						)
					);
				}
			}

			// Output
			if($output !== false) {
				return json_decode($output);
			}
			
			// Fail
			return false;
		}
	}
endif;