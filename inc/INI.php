<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Save data in INI file on secure way
 *
 * @since      7.0.0
 * @package    CF_Geoplugin
 * @author     Ivijan-Stefan Stipic
 * @url        https://github.com/CreativForm/PHP-Solutions/blob/master/class.os.php
 */
if (!class_exists('Serbian_Transliteration_INI', false)): class Serbian_Transliteration_INI
{
	public static function get( $key, $group = 'general', $default = NULL ) {
		$config = self::__get_ini();
		
		if( $group ) {
			if( isset($config[$group]) && isset($config[$group][$key]) ) {
				return $config[$group][$key];
			}
			
			return $default;
		} else {
			return $config[$key] ?? $default;
		}
	}
	
	public static function set( $key, $value, $group = 'general' ) {
		$config = self::__get_ini();
		
		if( $group ) {
			if( !isset($config[$group]) ) {
				$config[$group] = [];
			}
			$config[$group][$key] = $value;
		} else {
			$config[$key] = $value;
		}
		
		$iniContent = '';
		foreach ($config as $section => $values) {
			$iniContent .= '[' . esc_attr($section) . ']' . "\n";
			foreach ($values as $key => $value) {
				if(is_array($value)) {
					 foreach ($value as $subKey => $subValue) {
						$iniContent .= sprintf(
							((is_numeric($subValue) || is_bool($subValue)) ? '%s[%s] = %s' : '%s[%s] = "%s"'),
							$key,
							$subKey,
							esc_attr($subValue)
						);
					}
				} else {
					$iniContent .= sprintf(
						((is_numeric($value) || is_bool($value)) ? '%s = %s' : '%s = "%s"'),
						$key,
						esc_attr($value)
					);
				}
			}
			$iniContent .= "\n";
		}
		
		$wp_filesystem = new WP_Filesystem_Direct(null);
		$wp_filesystem->put_contents(self::__file_path(), $iniContent, (defined('FS_CHMOD_FILE') ? FS_CHMOD_FILE : '0644'));
		self::$__get_ini = NULL;		
		return $value;
	}
	
	
	public static function delete( $key, $value, $group = 'general' ) {
		$config = self::__get_ini();
		
		$update = false;
		
		if( $group ) {
			if( isset($config[$group]) && isset($config[$group][$key]) ) {
				unset($config[$group][$key]);
				$update = true;
			}
		} else if( isset($config[$key]) ) {
			unset($config[$key]);
			$update = true;
		}
		
		if( !$update ) {
			return false;
		}
		
		if( empty($config) ) {
			return true;
		}
		
		$iniContent = '';
		foreach ($config as $section => $values) {
			$iniContent .= '[' . esc_attr($section) . ']' . "\n";
			foreach ($values as $key => $value) {
				$iniContent .= sprintf(
					'%s = "%s"',
					$key,
					esc_attr($value)
				);
			}
			$iniContent .= "\n";
		}
		
		$wp_filesystem = new WP_Filesystem_Direct(null);
		$wp_filesystem->put_contents(self::__file_path(), $iniContent, (defined('FS_CHMOD_FILE') ? FS_CHMOD_FILE : '0644'));
		self::$__get_ini = NULL;		
		return $wp_filesystem->exists(self::__file_path());
	}
	
	private static $__get_ini = NULL;
	private static function __get_ini() {
		
		if( !class_exists('WP_Filesystem_Direct') ) {
			if( !class_exists('WP_Filesystem_Base') ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			}
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		}
		
		if( NULL === self::$__get_ini ) {
			$dir = dirname( self::__file_path() );
			
			if( !file_exists( $dir ) ) {

				$wp_filesystem = new WP_Filesystem_Direct(NULL);
				$wp_filesystem->mkdir($dir, (defined('FS_CHMOD_DIR') ? FS_CHMOD_DIR : '0755'));
			
				
				if (!$wp_filesystem->exists($dir.'/.htaccess')) {
					$wp_filesystem->put_contents($dir.'/.htaccess', trim("
Order deny,allow
Deny from all	"), (defined('FS_CHMOD_FILE') ? FS_CHMOD_FILE : '0644'));
				}
			}
			
			if( file_exists(self::__file_path()) ) {
				self::$__get_ini = parse_ini_file( self::__file_path(), true) ?? [];
			} else {
				self::$__get_ini = [];
			}
		}
		return self::$__get_ini ? self::$__get_ini : [];
		
	}
	
	private static function __file_path() {
		return WP_CONTENT_DIR . '/transliteration/' . apply_filters(
			'rstr/ini/filename',
			'rstr_' . str_rot13( substr(get_option(RSTR_NAME . '-ID'), 0, 24 )) . '.ini'
		);
	}
}
endif;