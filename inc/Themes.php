<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Include themes support if they are available
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Themes')) :
	class Serbian_Transliteration_Themes extends Serbian_Transliteration
	{
		private $themes = array(
			'themify'	=> 'themify',
			'avada'		=> 'avada'
		);
		private $theme;
		
		/* Run this script */
		public static function includes($options = array(), $only_object = false ) {
			global $rstr_cache;
			$class = get_called_class();
			if(!$class){
				$class = self::class;
			}
			$instance = $rstr_cache->get($class);
			if ( !$instance ) {
				$instance = $rstr_cache->set($class, new self($options, $only_object));
			}
			return $instance;
		}
		
		function __construct( $options=array(), $only_object = false ) {
			
			$wp_get_theme = wp_get_theme();
			
			if(empty($wp_get_theme) || !$wp_get_theme->exists()) return $this;
			
			if(RSTR_WOOCOMMERCE && get_rstr_option('mode') == 'woocommerce') return $this;
			
			$this->theme = strtolower($wp_get_theme->get('Name')); // gets the current theme
			if($only_object === false)
			{								
				$this->themes = apply_filters('rstr/themes', $this->themes);
				
				foreach($this->themes as $file_name=>$theme_name)
				{
					if ( strpos($this->theme, $theme_name) !== false || strpos($this->theme, $theme_name) !== false ) {
						$theme_class = "Serbian_Transliteration__Theme__{$file_name}";
						if(class_exists($theme_class)) {
							$theme_class::run();
						} else {
							include_once RSTR_INC . "/themes/{$file_name}.php";
							if(class_exists($theme_class)) {
								$theme_class::run();
							}
						}
					}
				}
			}
		}
		
		public function active_filters () {
			
			if(RSTR_WOOCOMMERCE && get_rstr_option('mode') == 'woocommerce') return array();
			
			// Include important function
			if(!function_exists('is_plugin_active')) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			
			$this->themes = apply_filters('rstr/themes', $this->themes);
			
			$return = array();
			
			foreach($this->themes as $file_name=>$theme_name)
			{
				if ( strpos($this->theme, $theme_name) !== false || strpos($this->theme, $theme_name) !== false ) {
					$theme_class = "Serbian_Transliteration__Theme__{$file_name}";
					if(class_exists($theme_class)) {
						$return = array_merge($return, $theme_class::filters());
					} else {
						include_once RSTR_INC . "/themes/{$file_name}.php";
						if(class_exists($theme_class)) {
							$return = array_merge($return, $theme_class::filters());
						}
					}
				}
			}
			
			return $return;
		}
	}
endif;