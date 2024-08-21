<?php if ( !defined('WPINC') ) die();


class Transliteration_Themes {

	public function __construct() {		
		$this->load_theme_class();
	}
	
	public function theme_class() {
		// Get the name of the active theme
		$theme = wp_get_theme();
		
		// Check if the theme has a parent theme
		if ($theme->parent()) {
			$theme_name = $theme->parent()->get('Name');
		} else {
			$theme_name = $theme->get('Name');
		}
		
		// Sanitize and construct the class name
		$class_name = 'Transliteration_Theme_' . $this->sanitize_class_name($theme_name);
		
		// Check if the class exists and instantiate it if it does
		if (class_exists($class_name)) {
			return $class_name;
		}
		
		return false;
	}
	
	private function sanitize_class_name($name) {
		$name = str_replace([' '], '_', $name);
		$name = preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
		$name = explode('_', $name);
		$name = array_map('ucfirst', $name);
		return join('_', $name);
	}
	
	private function load_theme_class() {
		$class_name = $this->theme_class();
		if ($class_name) {
			new $class_name();
		}
	}
}