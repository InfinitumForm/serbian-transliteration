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
		private $options;
		
		/* Run this script */
		private static $__run = NULL;
		public static function run($options = array()) {
			if( !self::$__run ) self::$__run = new self($options);
			return self::$__run;
		} 
		
		public static function filters ($options=array()) {
			if(empty($options)) $options = get_rstr_option();
			
			$filters = array(
				'comment_text'			=> 'content',
				'comments_template' 	=> 'content',
				'the_content' 			=> 'content',
				'the_title' 			=> 'content',
				'the_date' 				=> 'content',
				'get_post_time' 		=> 'content',
				'get_the_date' 			=> 'content',
				'the_content_more_link' => 'content',
				'wp_nav_menu_items' 	=> 'content',
				'wp_title' 				=> 'content',
				'pre_get_document_title'=> 'content',
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
				'option_blogdescription'=> 'content',
				'option_blogname' 		=> 'content',
				'document_title_parts' 	=> 'transliterate_objects'
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

		function __construct($options=false){
			if($options !== false)
			{
				$this->options = $options;
				
				$filters = self::filters($this->options);
				$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->options);

				if(!is_admin())
				{
					foreach($filters as $filter=>$function) $this->add_filter($filter, $function, (PHP_INT_MAX-1), 1);
					
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
		}
		
		function rss_output_buffer_start() {
			ob_start();
		}
		
		function rss_output_buffer_end() {
			$output = ob_get_clean();

			switch($this->get_current_script($this->options))
			{
				case 'cyr_to_lat' :
					$output = $this->cyr_to_lat($output);
					break;
					
				case 'lat_to_cyr' :
					$output = $this->lat_to_cyr($output);
					break;
			}

			echo $output;
		}
		
		public function bloginfo($output, $show=''){
			if(!empty($show) && in_array($show, array('name','description')))
			{
				switch($this->get_current_script($this->options))
				{
					case 'cyr_to_lat' :
						$output = $this->cyr_to_lat($output);
						break;
						
					case 'lat_to_cyr' :
						$output = $this->lat_to_cyr($output);			
						break;
				}
			}
			return $output;
		}
		
		public function content ($content='') {
			if(empty($content)) return $content;
			
			
			if(is_array($content))
			{
				$content = $this->transliterate_objects($content);
			}
			else if(is_string($content) && !is_numeric($content))
			{
					
				switch($this->get_current_script($this->options))
				{
					case 'cyr_to_lat' :
						$content = $this->cyr_to_lat($content);
						break;
						
					case 'lat_to_cyr' :
						$content = $this->lat_to_cyr($content);			
						break;
				}
			}
			return $content;
		}
	}
endif;