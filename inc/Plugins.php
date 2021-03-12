<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Include plugins support if they are available
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Plugins')) :
	class Serbian_Transliteration_Plugins extends Serbian_Transliteration
	{
		private $plugins = array(
			'revslider' => 'revslider',
			'woocommerce' => 'woocommerce',
			'wordpress-seo' => 'wp-seo',
			'data-tables-generator-by-supsystic' => 'index'
		);
		
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
			if($only_object === false)
			{				
				// Include important function
				if(!function_exists('is_plugin_active')) {
					include( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				
				$this->plugins = apply_filters('rstr/plugins', $this->plugins);
				
				foreach($this->plugins as $dir_name=>$file_name)
				{
					$addon = RSTR_INC . "/plugins/{$dir_name}.php";
					if( is_plugin_active("{$dir_name}/{$file_name}.php") && file_exists($addon) )
					{
						$class_name = str_replace(['-','.'], '_', $dir_name);
						$plugin_class = "Serbian_Transliteration__Plugin__{$class_name}";

						if(class_exists($plugin_class) && method_exists($plugin_class, 'run')) {
							$plugin_class::run();
						} else {
							include_once $addon;
							if(class_exists($plugin_class) && method_exists($plugin_class, 'run')) {
								$plugin_class::run();
							}
						}
					}
				}
			}
		}
		
		public function active_filters () {
			// Include important function
			if(!function_exists('is_plugin_active')) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			
			$this->plugins = apply_filters('rstr/plugins', $this->plugins);
			
			$return = array();
			
			foreach($this->plugins as $dir_name=>$file_name)
			{
				$addon = RSTR_INC . "/plugins/{$dir_name}.php";
				if( is_plugin_active("{$dir_name}/{$file_name}.php") && file_exists($addon) )
				{
					$class_name = str_replace(['-','.'], '_', $dir_name);
					$plugin_class = "Serbian_Transliteration__Plugin__{$class_name}";
					if(class_exists($plugin_class) && method_exists($plugin_class, 'filters')) {
						$return = array_merge($return, $plugin_class::filters());
					} else {
						include $addon;
						if(class_exists($plugin_class) && method_exists($plugin_class, 'filters')) {
							$return = array_merge($return, $plugin_class::filters());
						}
					}
				}
			}
			
			return $return;
		}
	}
endif;