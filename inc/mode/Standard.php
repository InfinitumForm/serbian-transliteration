<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Standard Transliteration Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if(!class_exists('Serbian_Transliteration_Mode_Standard')) :
	class Serbian_Transliteration_Mode_Standard extends Serbian_Transliteration
	{

		/* Run this script */
		public static function run() {
			global $rstr_cache;
			$class = self::class;
			$instance = $rstr_cache->get($class);
			if ( !$instance ) {
				$instance = $rstr_cache->set($class, new self());
			}
			return $instance;
		}

		public static function filters ($options=array()) {
			if(empty($options)) $options = get_rstr_option();

			$filters = array(
				'comment_text'			=> 'content',
				'comments_template' 	=> 'content',
				'the_content' 			=> 'content',
				'the_title' 			=> 'no_html_content',
				'the_date' 				=> 'no_html_content',
				'get_post_time' 		=> 'no_html_content',
				'get_the_date' 			=> 'no_html_content',
				'the_content_more_link' => 'content',
				'wp_nav_menu_items' 	=> 'content',
				'wp_title' 				=> 'no_html_content',
				'pre_get_document_title'=> 'no_html_content',
				'default_post_metadata'	=> 'content',
				'get_comment_metadata' 	=> 'content',
				'get_term_metadata' 	=> 'content',
				'get_user_metadata' 	=> 'content',
				'get_post_metadata' 	=> 'content',
				'get_page_metadata' 	=> 'content',
				'gettext' 				=> 'content',
				'ngettext' 				=> 'content',
				'gettext_with_context' 	=> 'content',
				'ngettext_with_context' => 'content',
				'option_blogdescription'=> 'no_html_content',
				'option_blogname' 		=> 'no_html_content',
				'document_title_parts' 	=> 'transliterate_objects',
				'get_the_terms'			=> 'transliteration_wp_terms',//Sydney, Blocksy, Colormag
				'wp_get_object_terms' 	=> 'transliteration_wp_terms', //Phlox
				'sanitize_title'		=> 'force_permalink_to_latin',
				'the_permalink'			=> 'force_permalink_to_latin',
				'wp_unique_post_slug'	=> 'force_permalink_to_latin'
			);
			asort($filters);

			if (!current_theme_supports( 'title-tag' )){
				unset($filters['document_title_parts']);
				unset($filters['pre_get_document_title']);
			} else {
				unset($filters['wp_title']);
			}

			return $filters;
		}

		function __construct(){
			$filters = self::filters($this->get_options());
			$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->get_options());

			if(!is_admin())
			{
				foreach($filters as $key=>$function){
					$this->add_filter($key, $function, (PHP_INT_MAX-1), 1);
				}

				if(get_rstr_option('enable-rss', 'no') == 'yes')
				{
					$this->add_action('rss_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('rss_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));

					$this->add_action('rss2_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('rss2_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));

					$this->add_action('rdf_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('rdf_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));

					$this->add_action('atom_head', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('atom_footer', 'rss_output_buffer_end', (PHP_INT_MAX-1));
				}

				if(get_rstr_option('force-widgets', 'no') == 'yes')
				{
					$this->add_action('dynamic_sidebar_before', 'rss_output_buffer_start', (PHP_INT_MAX-1));
					$this->add_action('dynamic_sidebar_after', 'rss_output_buffer_end', (PHP_INT_MAX-1));
				}

			}

			$this->add_filter('bloginfo', 'bloginfo', (PHP_INT_MAX-1), 2);
			$this->add_filter('bloginfo_url', 'bloginfo', (PHP_INT_MAX-1), 2);
		}

		function rss_output_buffer_start() {
			ob_start(NULL, 0, PHP_OUTPUT_HANDLER_REMOVABLE);
		}

		function rss_output_buffer_end() {
			$output = ob_get_clean();

			$output = $this->transliterate_text($output);

			echo $output;
		}

		/*
		 * Transliterate WP terms
		 * @contributor    Ivijan-Stefan Stipić
		 * @version        2.0.0
		**/
		public function transliteration_wp_terms($wp_terms)
		{
			if (!empty($wp_terms))
			{
				if(is_array($wp_terms))
				{
					foreach($wp_terms as $i => $term)
					{
						if(is_object($term) && ((isset($term->name) && !empty($term->name)) || (isset($term->description) && !empty($term->description))))
						{
							switch(Serbian_Transliteration_Utilities::get_current_script())
							{
								case 'cyr_to_lat' :
									if(isset($term->name) && !empty($term->name)){
										$wp_terms[$i]->name = $this->cyr_to_lat($term->name);
									}
									if(isset($term->description) && !empty($term->description)){
										$wp_terms[$i]->description = $this->cyr_to_lat($term->description);
									}
									break;
								case 'lat_to_cyr' :
									if(isset($term->name) && !empty($term->name)){
										$wp_terms[$i]->name = $this->lat_to_cyr($term->name);
									}
									if(isset($term->description) && !empty($term->description)){
										$wp_terms[$i]->description = $this->lat_to_cyr($term->description);
									}
									break;
							}
						}
					}
				}
			}

			return $wp_terms;
		}

		public function bloginfo($output, $show=''){
			if(!empty($show) && in_array($show, array('name','description')))
			{
				$output = $this->transliterate_text($output);
			}
			return $output;
		}

		public function content ($content='') {
			if(empty($content)) return $content;


			if(is_array($content))
			{
				if(method_exists($this, 'transliterate_objects')) {
					$content = $this->transliterate_objects($content);
				}
			}
			else if(is_string($content))
			{

				if(method_exists($this, 'transliterate_text')) {
					$content = $this->transliterate_text($content);
				}
			}
			return $content;
		}

		public function no_html_content ($content='') {
			if(empty($content)) return $content;

			if(is_array($content))
			{
				if(method_exists($this, 'transliterate_objects')) {
					$content = $this->transliterate_objects($content, NULL, false);
				}
			}
			else if(is_string($content))
			{

				if(method_exists($this, 'transliterate_text')) {
					$content = $this->transliterate_text($content, NULL, false);
				}
			}
			return $content;
		}
	}
endif;
