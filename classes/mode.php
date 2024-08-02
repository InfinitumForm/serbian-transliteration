<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Mode', false) ) : class Transliteration_Mode extends Transliteration {
	
	private $mode = NULL;
	
	/*
	 * The main constructor
	 */
	public function __construct() {
		// Disable transliteration
		if(get_rstr_option('transliteration-mode', 'cyr_to_lat') === 'none') {
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
	
	public function content( $content ) {
		return Transliteration_Controller::get()->transliterate($content);
	}
	
	public function no_html_content( $content ) {
		return Transliteration_Controller::get()->transliterate($content);
	}
	
} endif;