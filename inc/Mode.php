<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Forced Transliteration Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if(!class_exists('Serbian_Transliteration_Mode')) : class Serbian_Transliteration_Mode extends Serbian_Transliteration {
	
	/*
	 * Transliterate Posts results
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function get_posts ($posts) {
		
		foreach($posts as &$post) {
			if(method_exists($this, 'transliterate_text')) {
				$post->post_title = $this->transliterate_text($post->post_title);
				$post->post_content = $this->transliterate_text($post->post_content);
				$post->post_excerpt = $this->transliterate_text($post->post_excerpt);
			}
		}
		
		return $posts;
	}
	
	/*
	 * Transliterate Content (HTML & Text)
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function content($content = '') {
		if (empty($content)) {
			return $content;
		}

		if (is_array($content)) {
			return $this->objects($content);
		} elseif (is_string($content) && method_exists($this, 'transliterate_text')) {
			return $this->transliterate_text($content);
		}

		return $content;
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

		if (is_array($content)) {
			return $this->objects($content);
		} elseif (is_string($content) && method_exists($this, 'transliterate_text')) {
			return $this->transliterate_text($content);
		}

		return $content;
	}
	
	/*
	 * Force to Lat - Transliterate Content (HTML & Text)
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function content__force_lat($content = '') {
		if (empty($content)) {
			return $content;
		}

		if (is_array($content)) {
			return $this->objects($content);
		} elseif (is_string($content) && method_exists($this, 'transliterate_text')) {
			return $this->transliterate_text($content, 'cyr_to_lat');
		}

		return $content;
	}

	
	
	/*
	 * Transliterate no HTML content
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function no_html_content($content = '') {
		if (empty($content)) {
			return '';
		}

		if (is_array($content) && method_exists($this, 'transliterate_objects')) {
			$content = $this->transliterate_objects($content, NULL, false);
		} elseif (is_string($content) && method_exists($this, 'transliterate_text')) {
			$content = $this->transliterate_text($content, NULL, false);
		}

		return $content === NULL ? '' : $content;
	}

	
	/*
	 * Transliterate WP terms
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        2.0.0
	 **/
	public function transliteration_wp_terms($wp_terms) {
		if (empty($wp_terms) || !is_array($wp_terms)) {
			return $wp_terms;
		}

		$current_script = Serbian_Transliteration_Utilities::get_current_script();
		$transliterate_function = $current_script === 'cyr_to_lat' ? 'cyr_to_lat' : ($current_script === 'lat_to_cyr' ? 'lat_to_cyr' : null);

		if ($transliterate_function) {
			foreach ($wp_terms as $i => $term) {
				if (is_object($term)) {
					if (isset($term->name) && !empty($term->name)) {
						$wp_terms[$i]->name = $this->$transliterate_function($term->name);
					}
					if (isset($term->description) && !empty($term->description)) {
						$wp_terms[$i]->description = $this->$transliterate_function($term->description);
					}
				}
			}
		}

		return $wp_terms;
	}

	
	/*
	 * Transliterate WP Mails
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function wp_mail ($args) {
		if(!method_exists($this, 'transliterate_text')) return $args;
		
		if( $args['message'] ?? false ) {
			$args['message'] = $this->transliterate_text($args['message']);
		}
		
		if( $args['subject'] ?? false ) {
			$args['subject'] = $this->transliterate_text($args['subject']);
		}
		
		return $args;
	}
	
	/*
	 * Transliterate Image attributes
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function image_attributes($attributes) {
		if(!method_exists($this, 'transliterate_text')) return $attributes;
		
		foreach([
			'alt',
			'title'
		] as $attr) {
			if (isset($attributes[$attr])) {
				$attributes[$attr] = esc_attr( $this->transliterate_text($attributes[$attr]) );
			}
		}
		
		return $attributes;
	}
	
	
	/*
	 * Transliterate Blog informations
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function bloginfo($output, $show = '') {
		if (method_exists($this, 'transliterate_text') && !empty($show) && in_array($show, ['name', 'description'])) {
			$output = $this->transliterate_text($output);
		}
		return $output;
	}

	
	/*
	 * Transliterate only objects
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function objects ($obj) {
		if(method_exists($this, 'transliterate_objects')) {
			$obj = $this->transliterate_objects($obj);
		}
		
		return $obj;
	}
	
	
	/*
	 * Transliterate only label attr
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function label_attr ($field) {
		$field['label'] = $this->transliterate_text( $field['label'] );
		return $field;
	}
	
	
	/*
	 * Fix title parts
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function title_parts( $titles = array() ) {
		foreach ( $titles as $key => $val ) {
			if ( is_string( $val ) && ! is_numeric( $val ) ) {
				$titles[ $key ] = $this->cyr_to_lat( $titles[ $key ] );
			}
		}
		return $titles;
	}
	
	
	/*
	 * Transliterate JSON
	 * @contributor    Ivijan-Stefan Stipić
	 * @version        1.0.0
	 **/
	public function transliteration_json_content($json_content) {
		if (empty($json_content)) {
			return $json_content;
		}

		$content = json_decode($json_content, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return $json_content;
		}

		if (isset($content['locale_data']['messages']) && is_array($content['locale_data']['messages'])) {
			foreach ($content['locale_data']['messages'] as $key => $messages) {
				if (!is_array($messages)) {
					continue;
				}

				foreach ($messages as $key2 => $message) {
					$content['locale_data']['messages'][$key][$key2] = $this->cyr_to_lat($message);
				}
			}
		}

		return wp_json_encode($content);
	}

	
} endif;