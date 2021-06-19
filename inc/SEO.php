<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * SEO functionality
 *
 * @link              http://infinitumform.com/
 * @since             1.3.5
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_SEO')) :
	class Serbian_Transliteration_SEO extends Serbian_Transliteration
	{	
		public function __construct(){
			// Display alternate links
			if(defined('RSTR_ALTERNATE_LINKS') && RSTR_ALTERNATE_LINKS) {
				$this->add_action('wp_head', 'alternate_links', 1);
			}
			// Add generator
			$this->add_filter('the_generator', 'the_generator', 10, 2);
		}
	
		/**
		 * Initialize this class
		 * @version   1.0.0
		 */
		public static function init()
		{
			$class = get_called_class();
			if(!$class){
				$class = self::class;
			}
			$instance = Serbian_Transliteration_Cache::get($class);
			if ( !$instance ) {
				$instance = Serbian_Transliteration_Cache::set($class, new self());
			}
			return $instance;
		}
		
		/*
		 * Add generator for this plugin
		 * @since     1.0.13
		 * @verson    1.0.0
		*/
		public function the_generator($gen, $type){
			if(apply_filters('rstr/transliteration/generator', true))
			{
				switch ( $type )
				{
					case 'html':
						$gen.= PHP_EOL . '<meta name="generator" content="WordPress Transliterator ' . RSTR_VERSION . '">';
						break;
					case 'xhtml':
						$gen.= PHP_EOL . '<meta name="generator" content="WordPress Transliterator ' . RSTR_VERSION . '" />';
						break;
/*
					case 'atom':
						$gen.= PHP_EOL . '<generator uri="https://downloads.wordpress.org/plugin/serbian-transliteration.' . RSTR_VERSION . '.zip" version="' . RSTR_VERSION . '">WordPress Transliterator</generator>';
						break;
					case 'rss2':
						$gen.= PHP_EOL . '<generator>' . esc_url_raw( 'https://downloads.wordpress.org/plugin/serbian-transliteration.' . RSTR_VERSION . '.zip' ) . '</generator>';
						break;
					case 'rdf':
						$gen.= PHP_EOL . '<admin:generatorAgent rdf:resource="' . esc_url_raw( 'https://downloads.wordpress.org/plugin/serbian-transliteration.' . RSTR_VERSION . '.zip' ) . '" />';
						break;
					case 'comment':
						$gen.= PHP_EOL . '<!-- generator="WordPress Transliterator/' . RSTR_VERSION . '" -->';
						break;
					case 'export':
						$gen.= PHP_EOL . '<!-- generator="WordPress Transliterator/' . RSTR_VERSION . '" created="' . gmdate( 'Y-m-d H:i' ) . '" -->';
						break;
					default:
						if(preg_match('~<generator>(.*?)</generator>~i', $gen)){
							$gen.= PHP_EOL . '<generator>' . esc_url_raw( 'https://downloads.wordpress.org/plugin/serbian-transliteration.' . RSTR_VERSION . '.zip' ) . '</generator>';
						}
						break;
*/
				}
			}
			return $gen;
		}
		
		/*
		 * Alternate Links
		 * @since     1.0.13
		 * @verson    1.0.0
		*/
		public function alternate_links() {
			
			if(get_rstr_option('enable-alternate-links', 'yes') == 'no') return;
			if(apply_filters('rstr/alternate_links/disable', false)) return;
			
			$parse_url = Serbian_Transliteration_Utilities::parse_url();
			$url = $parse_url['url'];
			$locale = get_locale();
			$title = get_bloginfo('name');
			
			if(strpos($locale, '_') !== false){
				$hreflang_lat = strtr($locale, array('_'=>'_Latn_'));
				$hreflang_cyr = strtr($locale, array('_'=>'_Cyrl_'));
			} else {
				$hreflang_lat = $locale . '_Latn';
				$hreflang_cyr = $locale . '_Cyrl';
			}
		?>
<link rel="alternate" title="<?php echo esc_attr($this->lat_to_cyr($title, false)); ?>" href="<?php echo add_query_arg('rstr', 'cyr', $url); ?>" hreflang="<?php echo $hreflang_cyr; ?>" />
<link rel="alternate" title="<?php echo esc_attr($this->cyr_to_lat($title)); ?>" href="<?php echo add_query_arg('rstr', 'lat', $url); ?>" hreflang="<?php echo $hreflang_lat; ?>" />
		<?php
		}
		
		
		/**
		 * Get client IP address (high level lookup)
		 *
		 * @since	1.3.5
		 * @author  Ivijan-Stefan Stipic <creativform@gmail.com>
		 * @return  $string Client IP
		 */
		public function ip($blacklistIP=array())
		{
			
			if($ip = Serbian_Transliteration_Cache::get('IP')) return $ip;
			
			$findIP=apply_filters( 'rstr/seo/ip/constants', array_merge($findIP, array(
				'HTTP_CF_CONNECTING_IP', // Cloudflare
				'HTTP_X_FORWARDED_FOR', // X-Forwarded-For: <client>, <proxy1>, <proxy2> client = client ip address; https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-For
				'HTTP_X_FORWARDED',
				'HTTP_X_CLUSTER_CLIENT_IP', // Private LAN address
				'REMOTE_ADDR', // Most reliable way, can be tricked by proxy so check it after proxies
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED', // Forwarded: by=<identifier>; for=<identifier>; host=<host>; proto=<http|https>; https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Forwarded
				'HTTP_CLIENT_IP', // Shared Interner services - Very easy to manipulate and most unreliable way
			)) );
			
			$ip = '';
			// start looping
			
			foreach($findIP as $http)
			{
				if(empty($http)) continue;
				
				// Check in $_SERVER
				if (isset($_SERVER[$http]) && !empty($_SERVER[$http])){
					$ip=$_SERVER[$http];
				}
				
				// check in getenv() for any case
				if(empty($ip) && function_exists('getenv'))
				{
					$ip = getenv($http);
				}
				
				// Check if here is multiple IP's
				if(!empty($ip) && preg_match('/([,;]+)/', $ip))
				{
					$ips=str_replace(';',',',$ip);
					$ips=explode(',',$ips);
					$ips=array_map('trim',$ips);
					
					$ipf=array();
					foreach($ips as $ipx)
					{
						if($this->filter_ip($ipx, $blacklistIP) !== false)
						{
							$ipf[]=$ipx;
						}
					}
					
					$ipMAX=count($ipf);
					if($ipMAX>0)
					{
						if($ipMAX > 1)
						{
							if('HTTP_X_FORWARDED_FOR' == $http)
							{
								return Serbian_Transliteration_Cache::set('IP', $ipf[0]);
							}
							else
							{
								return Serbian_Transliteration_Cache::set('IP', end($ipf));
							}
						}
						else
							return Serbian_Transliteration_Cache::set('IP', $ipf[0]);
					}
					
					$ips = $ipf = $ipx = $ipMAX = NULL;
				}
				// Check if IP is real and valid
				if($this->filter_ip($ip, $blacklistIP)!==false)
				{
					return Serbian_Transliteration_Cache::set('IP', $ip);
				}
			}
			// let's try hacking into apache?
			if (function_exists('apache_request_headers')) {
				$headers = apache_request_headers();
				if (
					array_key_exists( 'X-Forwarded-For', $headers ) 
					&& filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )  
					&& in_array($headers['X-Forwarded-For'], $blacklistIP,true)===false
				){
					
					// Well Somethimes can be tricky to find IP if have more then one
					$ips=str_replace(';',',',$headers['X-Forwarded-For']);
					$ips=explode(',',$ips);
					$ips=array_map('trim',$ips);
					
					$ipf=array();
					foreach($ips as $ipx)
					{
						if($this->filter_ip($ipx, $blacklistIP)!==false)
						{
							$ipf[]=$ipx;
						}
					}
					
					$ipMAX=count($ipf);
					if($ipMAX>0)
					{
						/*if($ipMAX > 1)
							return end($ipf);
						else*/
						return Serbian_Transliteration_Cache::set('IP', $ipf[0]);
					}
					
					$ips = $ipf = $ipx = $ipMAX = NULL;
				}
			}
			// let's try the last thing, why not?
			if( self::is_connected() )
			{
				$result = NULL;
				
				if(function_exists('file_get_contents'))
				{
					$context = self::set_stream_context( array( 'Accept: application/json' ), 'GET' );
					$result = @file_get_contents( 'https://api.ipify.org/?format=json', false, $context );
				}
				if($result)
				{
					$result = json_decode($result);
					if(isset($result->ip))
					{
						$ip = $result->ip;
						if($this->filter_ip($ip)!==false)
						{
							return Serbian_Transliteration_Cache::set('IP', $ip);
						}
					}
				}
			}
			// Let's ask server?
			if(stristr(PHP_OS, 'WIN'))
			{
				if(function_exists('shell_exec'))
				{
					$ip = shell_exec('powershell.exe -InputFormat none -ExecutionPolicy Unrestricted -NoProfile -Command "(Invoke-WebRequest https://api.ipify.org).Content.Trim()"');
					if($this->filter_ip($ip)!==false)
					{
						return Serbian_Transliteration_Cache::set('IP', $ip);
					}
					
					$ip = shell_exec('powershell.exe -InputFormat none -ExecutionPolicy Unrestricted -NoProfile -Command "(Invoke-WebRequest https://smart-ip.net/myip).Content.Trim()"');
					if($this->filter_ip($ip)!==false)
					{
						return Serbian_Transliteration_Cache::set('IP', $ip);
					}
					
					$ip = shell_exec('powershell.exe -InputFormat none -ExecutionPolicy Unrestricted -NoProfile -Command "(Invoke-WebRequest https://ident.me).Content.Trim()"');
					if($this->filter_ip($ip)!==false)
					{
						return Serbian_Transliteration_Cache::set('IP', $ip);
					}
				}
			}
			else
			{
				if(function_exists('shell_exec'))
				{
					$ip = shell_exec('curl https://api.ipify.org##*( )');
					if($this->filter_ip($ip)!==false)
					{
						return Serbian_Transliteration_Cache::set('IP', $ip);
					}
					
					$ip = shell_exec('curl https://smart-ip.net/myip##*( )');
					if($this->filter_ip($ip)!==false)
					{
						return Serbian_Transliteration_Cache::set('IP', $ip);
					}
					
					$ip = shell_exec('curl https://ident.me##*( )');
					if($this->filter_ip($ip)!==false)
					{
						return Serbian_Transliteration_Cache::set('IP', $ip);
					}
				}
			}
			
			// OK, this is the end :(
			return false;
		}
		
		
		/*
		 * PRIVATE: Is crawler or robot
		 * @since     1.3.5
		 * @verson    1.0.0
		*/
		private function is_crawler($ip){
			// IP => RANGE
			$range=apply_filters('rstr/seo/is_crawler/range', array(
				// Google
				'64.18.0.0'			=>	'64.18.15.255',
				'64.233.160.0'		=>	'64.233.191.255',
				'66.249.64.0'		=>	'66.249.95.255 ',
				'66.102.0.0'		=>	'66.102.15.255',
				'72.14.192.0'		=>	'72.14.255.255',
				'74.125.0.0'		=>	'74.125.255.255',
				'108.177.8.0'		=>	'108.177.15.255',
				'172.217.0.0'		=>	'172.217.31.255',
				'173.194.0.0'		=>	'173.194.255.255',
				'207.126.144.0'		=>	'207.126.159.255',
				'209.85.128.0'		=>	'209.85.255.255',
				'216.58.192.0'		=>	'216.58.223.255',
				'216.239.32.0'		=>	'216.239.63.255',
				// MSN
				'64.4.0.0'			=>	'64.4.63.255 ',
				'65.52.0.0'			=>	'65.55.255.255 ',
				'131.253.21.0'		=>	'131.253.47.255',
				'157.54.0.0'		=>	'157.60.255.255',
				'207.46.0.0'		=>	'207.46.255.255',
				'207.68.128.0'		=>	'207.68.207.255',
				// Yahoo
				'8.12.144.0'		=>	'8.12.144.255 ',
				'66.196.64.0'		=>	'66.196.127.255 ',
				'66.228.160.0'		=>	'66.228.191.255 ',
				'67.195.0.0'		=>	'67.195.255.255 ',
				'68.142.192.0'		=>	'68.142.255.255 ',
				'72.30.0.0'			=>	'72.30.255.255',
				'74.6.0.0'			=>	'74.6.255.255',
				'98.136.0.0'		=>	'98.139.255.255',
				'202.160.176.0'		=>	'202.160.191.255',
				'209.191.64.0'		=>	'209.191.127.255',
				// Bing
				'104.146.0.0'		=>	'104.146.63.255',
				'104.146.100.0'		=>	'104.146.113.255',
				// Yandex
				'100.43.64.0'		=>	'100.43.79.255',
				'100.43.80.0'		=>	'100.43.83.255',
				// Baidu
				'103.6.76.0'		=>	'103.6.79.255',
				'104.193.88.0'		=>	'104.193.91.255',
				'106.12.0.0'		=>	'106.13.255.255',
				'115.231.36.136'	=>	'115.231.36.159',
				'39.156.69.79',
				'220.181.38.148',
				// DuckDuckGo
				'50.16.241.113'		=>	'50.16.241.117',
				'54.208.100.253'	=>	'54.208.102.37',
				'72.94.249.34'		=>	'72.94.249.38',
				'23.21.227.69',
				'40.88.21.235',
				'50.16.247.234',
				'52.204.97.54',
				'52.5.190.19',
				'54.197.234.188',
				'107.21.1.8',
				// Sogou
				'118.191.216.42'	=>	'118.191.216.57',
				'119.28.109.132',
				// Ask
				'65.214.45.143'		=>	'65.214.45.148',
				'66.235.124.7',
				'66.235.124.101',
				'66.235.124.193',
				'66.235.124.73',
				'66.235.124.196',
				'66.235.124.74',
				'63.123.238.8',
				'202.143.148.61',
				// Pinterest
				'54.236.1.1'		=>	'54.236.1.255',
				'54.82.14.182',
				'54.81.171.36',
				'23.20.24.147',
				'54.237.150.66',
				'54.237.197.55',
				'54.211.68.214',
				'54.234.164.192',
				'50.16.155.205',
				'23.20.84.153',
				'54.224.131.213',
				// Facebook
				'69.63.176.0'		=>	'69.63.176.21',
				'69.63.184.0'		=>	'69.63.184.21',
				'66.220.144.0'		=>	'66.220.144.21',
				'69.63.176.0'		=>	'69.63.176.20',
				'31.13.24.0'		=>	'31.13.24.21',
				'31.13.64.0'		=>	'31.13.64.18',
				'69.171.224.0'		=>	'69.171.224.19',
				'74.119.76.0'		=>	'74.119.76.22',
				'103.4.96.0'		=>	'103.4.96.22',
				'173.252.64.0'		=>	'173.252.64.18',
				'204.15.20.0'		=>	'204.15.20.22',
				// Twitter
				'199.59.156.0'		=>	'199.59.156.255',
				// Linkedin
				'144.2.22.0'		=>	'144.2.22.24',
				'144.2.224.0'		=>	'144.2.224.24',
				'144.2.225.0'		=>	'144.2.225.24',
				'144.2.228.0'		=>	'144.2.228.24',
				'144.2.229.0'		=>	'144.2.229.24',
				'144.2.233.0'		=>	'144.2.233.24',
				'144.2.237.0'		=>	'144.2.237.24',
				'216.52.16.0'		=>	'216.52.16.24',
				'216.52.17.0'		=>	'216.52.17.24',
				'216.52.18.0'		=>	'216.52.18.24',
				'216.52.20.0'		=>	'216.52.20.24',
				'216.52.21.0'		=>	'216.52.21.24',
				'216.52.22.0'		=>	'216.52.22.24',
				'65.156.227.0'		=>	'65.156.227.24',
				'8.39.53.0'			=>	'8.39.53.24'
			), $ip);

			ksort($range);
			
			$ip2long = ip2long($ip);
			
			if($ip2long !== false)
			{
				foreach($range as $start => $end)
				{
					$end = ip2long($end);
					$start = ip2long($start);
					$is_key = ($start === false);
					
					if($end === false) continue;
					
					if(is_numeric($start) && $is_key && $end === $ip2long)
					{
						return apply_filters('rstr/seo/is_crawler/return', $ip, $range);
					}
					else
					{
						if(!$is_key && $ip2long >= $start && $ip2long <= $end)
						{
							return apply_filters('rstr/seo/is_crawler/return', $ip, $range);
						}
					}
				}
			}
			
			return apply_filters('rstr/seo/is_crawler/return', false, $range);
		}		
		
		/*
		 * PRIVATE: Validate IP address
		 * @since     1.3.5
		 * @verson    1.0.0
		*/
		private function validate_ip( $ip ){
		
			$ip = str_replace(array("\r", "\n", "\r\n", "\s"), '', $ip);
			
			if(function_exists("filter_var") && !empty($ip) && filter_var($ip, FILTER_VALIDATE_IP) !== false)
			{
				return $ip;
			}
			else if(!empty($ip) && preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $ip))
			{
				return $ip;
			}
			
			return false;
		}		
		
		/**
		 * PRIVATE: Set stream context
		 * @since	1.3.5
		 */
		private static function set_stream_context( $header = array(), $method = 'POST', $content = '' )
		{	
			$header = array_merge( array( 'Content-Type: application/x-www-form-urlencoded' ), $header );
			
			return stream_context_create(
				array(
					'http' => array(
						'method'  	=> $method,
						'header' 	=> $header,
						'content'	=> $content	
					)
				)
			);
		}
		
		/**
		 * PRIVATE: Check is IP valid or not
		 *
		 * @since	1.3.5
		 * @author  Ivijan-Stefan Stipic <creativform@gmail.com>
		 * @return  (string) IP address or (bool) false
		 */
		private function filter_ip($ip, $blacklistIP=array())
		{
			if(
				function_exists('filter_var') 
				&& !empty($ip) 
				&& in_array($ip, $blacklistIP,true)===false 
				&& filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false
			) {
				return $ip;
			} else if(
				preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $ip) 
				&& !empty($ip) 
				&& in_array($ip, $blacklistIP,true)===false
			) {
				return $ip;
			}
			
			return false;
		}
		
		/*
		 * CHECK INTERNET CONNECTION
		 * @since	7.0.0
		 * @return	true/false
		 */
		public static function is_connected()
		{
			// List connections
			$urls = array(
				'www.google.com',
				'www.facebook.com'
			);
			foreach($urls as $url)
			{
				// list ports
				foreach(array(443,80) as $port)
				{
					$connected = fsockopen($url, $port);
					if ($connected !== false){
						fclose($connected);
						return true;
					}
				}
			}
	
			// OK you not have connection - boohooo
			return false;
		}
	}
endif;