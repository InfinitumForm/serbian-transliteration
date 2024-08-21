<?php if ( !defined('WPINC') ) die();

class Transliteration {
    // Current instance
    public static $self = NULL;

    /**
     * Constructor
     *
     * @param mixed $data Object or array of data to initialize properties.
     */
    public function __construct($data = NULL) {
        self::$self = $this;

        if ($data === NULL) {
            return;
        }

        if (is_object($data)) {
            $this->initialize_properties($data);
        } elseif (is_array($data)) {
            $this->initialize_properties((object)$data);
        }
    }

    /**
     * Initialize properties from given data
     *
	 * @type private
	 *
     * @param object $data Data to initialize properties.
     */
    private function initialize_properties($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $this->sanitize($value);
            }
        }
    }
	
	/**
     * Run the plugin
     */
	public static function run_the_plugin() {
		new Transliteration_Init();
	}

    /**
     * Magic method to handle dynamic calls to properties
     *
     * @param string $function Method name.
     * @param array $arguments Method arguments.
	 *
     * @return mixed
     * @throws Exception if the method does not exist.
     */
    public function __call($function, $arguments) {
        if (property_exists($this, $function)) {
            return $this->$function;
        }

        // Handle dynamic setters
        if (substr($function??'', 0, 3) === 'set') {
            $property = lcfirst(substr($function??'', 3));
            if (property_exists($this, $property)) {
                $this->$property = $this->sanitize($arguments[0]);
                return $this;
            }
        }

        throw new Exception('No such method: ' . get_class($this) . '->' . $function . '()');
    }

    /**
     * Get method for properties
     *
     * @param string $property Property name.
	 *
     * @return mixed
	 * @throws Exception if the property does not exist.
     */
    public function get_property(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new Exception('No such property: ' . get_class($this) . '->' . $property);
    }

    /**
     * Set method for properties
     *
     * @param string $property Property name.
     * @param mixed $value Property value.
	 *
     * @return $this
	 * @throws Exception if the property does not exist.
     */
    public function set_property(string $property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $this->sanitize($value);
            return $this;
        }

        throw new Exception('No such property: ' . get_class($this) . '->' . $property);
    }
	
	/*
	 * Helper for add_action()
	 * @author        Ivijan-Stefan Stipic
	 */
	public function ob_start($callback = null, int $chunk_size = 0, int $flags = PHP_OUTPUT_HANDLER_REMOVABLE){
		if(!is_array($callback) && !is_callable($callback)){
			$callback = array(&$this, $callback);
		}
		ob_start($callback, $chunk_size, $flags);
	}
	
	/*
	 * Helper for add_action()
	 * @author        Ivijan-Stefan Stipic
	 */
	public function add_action(string $tag, $function_to_add, int $priority = 10, int $accepted_args = 1){
		if(!is_array($function_to_add)){
			$function_to_add = array(&$this, $function_to_add);
		}
		return add_action( (string)$tag, $function_to_add, (int)$priority, (int)$accepted_args );
	}

	/*
	 * Helper for remove_action()
	 * @author        Ivijan-Stefan Stipic
	 */
	public function remove_action(string $tag, $function_to_remove, int $priority = 10){
		if(!is_array($function_to_remove)){
			$function_to_remove = array(&$this, $function_to_remove);
		}
		return remove_action( $tag, $function_to_remove, $priority );
	}

	/*
	 * Helper for add_filter()
	 * @author        Ivijan-Stefan Stipic
	 */
	public function add_filter(string $tag, $function_to_add, int $priority = 10, int $accepted_args = 1){
		if(!is_array($function_to_add)){
			$function_to_add = array(&$this, $function_to_add);
		}
		return add_filter( (string)$tag, $function_to_add, (int)$priority, (int)$accepted_args );
	}

	/*
	 * Helper for remove_filter()
	 * @author        Ivijan-Stefan Stipic
	 */
	public function remove_filter(string $tag, $function_to_remove, int $priority = 10){
		if(!is_array($function_to_remove)){
			$function_to_remove = array(&$this, $function_to_remove);
		}
		return remove_filter( (string)$tag, $function_to_remove, (int)$priority );
	}

	/*
	 * Helper for add_shortcode()
	 * @author        Ivijan-Stefan Stipic
	 */
	public function add_shortcode(string $tag, $function_to_add){
		if(!is_array($function_to_add)){
			$function_to_add = array(&$this, $function_to_add);
		}
		if(!shortcode_exists($tag)) {
			return add_shortcode( $tag, $function_to_add );
		}

		return false;
	}
	
	/**
     * Sanitize values
     *
	 * @type protected
	 *
     * @param mixed $object Any object type that need to be sanitized.
	 *
     * @return mixed
     */
	public function sanitize($object) {
		if (is_array($object)) {
			return array_map( [&$this, 'tp_sanitize'], $object );
		}

		if (is_numeric($object)) {
			if ($object == ($intval = intval($object))) {
				return $intval;
			} elseif ($object == ($floatval = floatval($object))) {
				return $floatval;
			}
		} elseif (is_string($object)) {
			if (filter_var($object, FILTER_VALIDATE_EMAIL)) {
				return sanitize_email($object);
			} elseif (filter_var($object, FILTER_VALIDATE_URL)) {
				return esc_url($object);
			} elseif ($object != strip_tags($object)) {
				return wp_kses_post(sanitize_textarea_field($object));
			} else {
				return sanitize_text_field($object);
			}
		}

		return apply_filters('transliterator_sanitize', $object, $this);
	}


}