<?php if (!defined('WPINC')) die();

class Transliteration_Plugins {

	public function __construct() {	
		$this->load_plugin_classes();
	}
	
	public function plugin_classes() {
		// Get the list of active plugins
		$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
		
		$found_classes = [];
		foreach ($active_plugins as $plugin) {
			// Extract the plugin folder name from the plugin path
			$plugin_folder = basename(dirname($plugin));
			
			// Sanitize and construct the class name
			$class_name = 'Transliteration_Plugin_' . $this->sanitize_class_name($plugin_folder);
			
			// Check if the class exists and add it to the array if it does
			if (class_exists($class_name)) {
				$found_classes[] = $class_name;
			}
		}
		
		return $found_classes;
	}
	
	private function sanitize_class_name($name) {
		$name = str_replace([' '], '_', $name);
		$name = preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
		$name = explode('_', $name);
		$name = array_map('ucfirst', $name);
		return join('_', $name);
	}
	
	private function load_plugin_classes() {
		$classes = $this->plugin_classes();
		foreach ($classes as $class_name) {
			new $class_name();
		}
	}
}