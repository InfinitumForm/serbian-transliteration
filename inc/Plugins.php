<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Include plugins support if they are available
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Plugins', false)) :
	class Serbian_Transliteration_Plugins extends Serbian_Transliteration
	{
		private $plugins = array(
			'revslider' => 'revslider',
			'woocommerce' => 'woocommerce',
			'wordpress-seo' => 'wp-seo',
			'data-tables-generator-by-supsystic' => 'index',
			'contact-form-7' => 'wp-contact-form-7',
			'advanced-custom-fields' => 'acf',
			'advanced-custom-fields-pro' => 'acf',
			'elementor' => 'elementor',
			'elementor-pro' => 'elementor'
		);
		
		/* Run this script */
		public static function includes($options = array(), $only_object = false ) {
			$class = self::class;
			
			$instance = Serbian_Transliteration_Cache::get($class);
			if ( !$instance ) {
				$instance = Serbian_Transliteration_Cache::set($class, new self($options, $only_object));
			}
			return $instance;
		}
		
		function __construct($options = array(), $only_object = false) {
			if ($only_object === false) {

				$this->plugins = apply_filters('rstr/plugins', $this->plugins);
				
				if( get_rstr_option('mode', 'advanced') == 'light' ) {
					unset($this->plugins['woocommerce']);
				}

				foreach ($this->plugins as $dir_name => $file_name) {
					$addon = RSTR_INC . "/plugins/{$dir_name}.php";
					if (Serbian_Transliteration_Utilities::is_plugin_active("{$dir_name}/{$file_name}.php") && file_exists($addon)) {
						$class_name = str_replace(['-', '.'], '_', $dir_name);
						$plugin_class = "Serbian_Transliteration__Plugin__{$class_name}";

						include_once $addon;
						if (class_exists($plugin_class, false) && method_exists($plugin_class, 'run')) {
							$plugin_class::run();
						}
					}
				}
			}
		}

		
		public function active_filters() {
			$this->plugins = apply_filters('rstr/plugins', $this->plugins);
			
			if( get_rstr_option('mode', 'advanced') == 'light' ) {
				unset($this->plugins['woocommerce']);
			}

			$return = [];

			foreach ($this->plugins as $dir_name => $file_name) {
				$addon = RSTR_INC . "/plugins/{$dir_name}.php";
				if (Serbian_Transliteration_Utilities::is_plugin_active("{$dir_name}/{$file_name}.php") && file_exists($addon)) {
					include_once $addon;

					$class_name = str_replace(['-', '.'], '_', $dir_name);
					$plugin_class = "Serbian_Transliteration__Plugin__{$class_name}";

					if (class_exists($plugin_class, false) && method_exists($plugin_class, 'filters')) {
						$return = array_merge($return, $plugin_class::filters());
					}
				}
			}

			return $return;
		}
	}
endif;