<?php

if (!defined('WPINC')) {
    die();
}

class Transliteration_Plugins
{
    /**
     * Cached list of active plugin integration classes.
     *
     * @var array|null
     */
    private static ?array $cached_classes = null;

    public function __construct()
    {
        $this->load_plugin_classes();
    }

    /**
     * Discover active plugin integration classes.
     * Results are cached for subsequent calls within the request.
     */
    public function plugin_classes()
    {
        if (self::$cached_classes !== null) {
            return self::$cached_classes;
        }

        // Get the list of active plugins
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

        // Filter active plugins
        $active_plugins = apply_filters('rstr_active_plugins', $active_plugins);

        $found_classes = [];
        foreach ($active_plugins as $plugin) {
            // Extract the plugin folder name from the plugin path
            $plugin_folder = basename(dirname($plugin));

            // Sanitize and construct the class name
            $class_name = 'Transliteration_Plugin_' . $this->sanitize_class_name($plugin_folder);

            // Filter current class
            $class_name = apply_filters('rstr_active_plugin_class', $class_name, $plugin_folder, $plugin);

            // Check if the class exists and add it to the array if it does
            if ($class_name && class_exists($class_name)) {
                $found_classes[] = $class_name;
            }
        }

        // Filter all classes
        $found_classes = apply_filters('rstr_active_plugin_classes', $found_classes, $active_plugins);

        return self::$cached_classes = $found_classes;
    }

    private function sanitize_class_name(string $name): string
    {
        $name = str_replace([' '], '_', $name);
        $name = preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
        $name = explode('_', $name);
        $name = array_map('ucfirst', $name);
        return implode('_', $name);
    }

    private function load_plugin_classes(): void
    {
        $classes = $this->plugin_classes();
        foreach ($classes as $class_name) {
            new $class_name();
        }
    }

    /**
     * Reset cached plugin classes.
     */
    public static function clear_cache(): void
    {
        self::$cached_classes = null;
    }
}
