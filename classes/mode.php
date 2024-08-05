<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Mode', false) ) : class Transliteration_Mode extends Transliteration {
	
	private $mode = NULL;
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		// Disable transliteration
		if(get_rstr_option('transliteration-mode', 'cyr_to_lat') === 'none' || Transliteration_Controller::get()->disable_transliteration()) {
			return;
		}
		// Load transliteration
		$this->load_mode();
		// Add exclusion filter
		$this->add_filter('transliteration_exclude_filters', 'exclude_filters', 10);
		// Apply transliteration filters
		$this->apply_filters();
    }
	
	/*
	 * Get current instance
	 */
	private static $instance = NULL;
	public static function get() {
		if( NULL === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}	
	
	/*
	 * The current mode
	 */
	public function mode( $mode = NULL ) {
		static $mode_class = [];
		
		$cache_key = $mode ?? 0;
		
		if( array_key_exists($cache_key, $mode_class) ) {
			return $mode_class[$cache_key];
		}
		
		if( $mode && in_array($mode, array_keys( Transliteration_Utilities::plugin_mode() )) ) {
			$current_mode = $mode;
		} else {
			$current_mode = get_rstr_option('mode', 'light');
		}
		
		if( $current_mode ) {
			if( class_exists( $mode = 'Transliteration_Mode_' . ucfirst($current_mode) ) ) {
				$mode_class[$cache_key] = $mode;
			}
		}
		
		return $mode_class[$cache_key] ?? [];
	}
	
	/*
	 * Exclude filters
	 */
	public function exclude_filters($filters) {
		if( $remove_filters = get_rstr_option('transliteration-filter', []) ) {
			return array_diff_key($filters, array_flip($remove_filters));
		}
		return $filters;
	}
	
	/*
	 * Load the current mode
	 */
	private function load_mode() {
		if( empty($this->mode) && ( $mode_class = $this->mode() ) ) {
			$this->mode = $mode_class::get();
		}
	}
	
	/*
	 * Apply filters for current mode
	 */
	private function apply_filters() {
		$filters = $this->mode->filters();
		
		$filters = apply_filters('transliteration_exclude_filters', $filters);
		$filters = apply_filters('transliteration_exclude_filters_' . $this->mode::MODE, $filters);
		
		$filters = apply_filters_deprecated('rstr/transliteration/exclude/filters', [$filters], '2.0.0', 'transliteration_exclude_filters');
		$filters = apply_filters_deprecated('rstr/transliteration/exclude/filters/' . $this->mode::MODE, [$filters], '2.0.0', 'transliteration_exclude_filters_' . $this->mode::MODE);
		
		if( empty($filters) ) {
			return;
		}
		
		if ( $filters && ( !is_admin() || wp_doing_ajax() ) ) {
			foreach ($filters as $key => $method) {
				$args = $key === 'gettext' ? 3 : 1;

				if( method_exists($this, $method) ) {
					$this->add_filter($key, $method, $args);
				}
			}
		}
	}
	
	/**
	 * Transliterate any content
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function content( $content ) {
		return Transliteration_Controller::get()->transliterate($content);
	}
	
	/**
	 * Transliterate text
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function no_html_content( $content ) {
		return Transliteration_Controller::get()->transliterate_no_html($content);
	}
	
	/**
	 * Transliterate Objects
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function transliterate_objects($data) {
		if (is_array($data)) {
			// Ako je data niz, rekurzivno prolazimo kroz sve elemente niza
			foreach ($data as &$value) {
				if (is_array($value) || is_object($value)) {
					$value = $this->transliterate_objects($value);
				} elseif (is_string($value)) {
					$value = Transliteration_Controller::get()->transliterate($value);
				}
			}
		} elseif (is_object($data)) {
			// Ako je data objekat, prolazimo kroz sve njegove javne varijable
			foreach ($data as $key => $value) {
				if (is_array($value) || is_object($value)) {
					$data->$key = $this->transliterate_objects($value);
				} elseif (is_string($value)) {
					$data->$key = Transliteration_Controller::get()->transliterate($value);
				}
			}
		}

		return $data;
	}
	
	/**
	 * Transliterate WP Terms
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function transliteration_wp_terms($wp_terms) {
		if (empty($wp_terms) || !is_array($wp_terms)) {
			return $wp_terms;
		}

		foreach ($wp_terms as $i => $term) {
			if (is_object($term)) {
				if (isset($term->name) && !empty($term->name)) {
					$wp_terms[$i]->name = Transliteration_Controller::get()->transliterate($term->name);
				}
				if (isset($term->description) && !empty($term->description)) {
					$wp_terms[$i]->description = Transliteration_Controller::get()->transliterate($term->description);
				}
			}
		}

		return $wp_terms;
	}
	
	/**
	 * Force all permalinks to latin
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function force_permalink_to_latin ($permalink) {
		$permalink = rawurldecode($permalink);
		$permalink= Transliteration_Controller::get()->cyr_to_lat_sanitize($permalink);
		return $permalink;
	}
	
	/**
	 * Transliterate Image attributes
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function image_attributes($attributes) {		
		return Transliteration_Controller::get()->transliterate_attributes($attributes, ['alt', 'title']);
	}
	
	/**
	 * Transliterate WP Mails
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 */
	public function wp_mail ($args) {
		
		if( get_rstr_option('force-email-transliteration', 'yes') === 'no' ) {
			return $args;
		}
		
		if( $args['message'] ?? false ) {
			$args['message'] = Transliteration_Controller::get()->transliterate($args['message']);
		}
		
		if( $args['subject'] ?? false ) {
			$args['subject'] = Transliteration_Controller::get()->transliterate($args['subject']);
		}
		
		return $args;
	}
	
	/*
	 * Transliterate gettext (HTML & Text)
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function gettext_content($content, $text = '', $domain = '') {
		if (empty($content)) {
			return $content;
		}

		if ( is_array($content) ) {
			return $this->transliterate_objects($content);
		} else if ( is_string($content) ) {
			return Transliteration_Controller::get()->transliterate($content);
		}

		return $content;
	}
	
} endif;