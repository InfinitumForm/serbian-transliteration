<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Forced Transliteration Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Mode_Forced')) :
class Serbian_Transliteration_Mode_Forced extends Serbian_Transliteration
{
	private $options;
	
	function __construct($options){
		
		$this->options = $options;
		$transient = 'transliteration_cache_' . $this->get_current_script($this->options) . '_' . $this->get_current_page_ID();

		$filters = array(
			'comments_template' 			=> 'content',
			'the_content' 					=> 'content',
			'the_title' 					=> 'content',
			'wp_nav_menu_items' 			=> 'content',
			'get_post_time' 				=> 'content',
			'wp_title' 						=> 'content',
			'the_date' 						=> 'content',
			'get_the_date' 					=> 'content',
			'the_content_more_link' 		=> 'content',
			'pre_get_document_title'		=> 'content',
			'default_post_metadata' 		=> 'content',
			'get_comment_metadata' 			=> 'content',
			'get_term_metadata' 			=> 'content',
			'get_user_metadata' 			=> 'content',
			'gettext' 						=> 'content',
			'ngettext' 						=> 'content',
			'gettext_with_context' 			=> 'content',
			'ngettext_with_context' 		=> 'content',
			'widget_text' 					=> 'content',
			'widget_title' 					=> 'content',
			'widget_text_content' 			=> 'content',
			'widget_custom_html_content' 	=> 'content',
			'sanitize_title' 				=> 'content',
			'wp_unique_post_slug' 			=> 'content',
			'document_title_parts' 			=> 'title_parts',
			'sanitize_title'				=> 'force_permalink_to_latin',
			'the_permalink'					=> 'force_permalink_to_latin',
			'wp_unique_post_slug'			=> 'force_permalink_to_latin'
		);
		
		if(isset($this->options['avoid-admin']) && $this->options['avoid-admin'] == 'yes')
		{
			if(!is_admin())
			{
				foreach($filters as $filter=>$function) $this->add_filter($filter, $function, 9999999, 1);
			}
		}
		else
		{
			foreach($filters as $filter=>$function) $this->add_filter($filter, $function, 9999999, 1);
		}
		
		if(!is_admin())
		{
			$this->add_action('wp_loaded', 'output_buffer_start', 999);
			$this->add_action('shutdown', 'output_buffer_end', 999);
			
			$this->add_action('rss_head', 'rss_output_buffer_start', 999);
			$this->add_action('rss_footer', 'rss_output_buffer_end', 999);
			
			$this->add_action('rss2_head', 'rss_output_buffer_start', 999);
			$this->add_action('rss2_footer', 'rss_output_buffer_end', 999);
			
			$this->add_action('rdf_head', 'rss_output_buffer_start', 999);
			$this->add_action('rdf_footer', 'rss_output_buffer_end', 999);
			
			$this->add_action('atom_head', 'rss_output_buffer_start', 999);
			$this->add_action('atom_footer', 'rss_output_buffer_end', 999);
		}
	}
	
	function output_buffer_start() { 
		ob_start(array(&$this, "output_callback"));
	}
	
	function output_buffer_end() { 
		ob_get_clean();
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
	
	public function output_callback ($buffer='') {
		if(empty($buffer)) return $buffer;
		
		if(!(defined('DOING_AJAX') && DOING_AJAX))
		{
			$sufix = '_' . strlen($buffer);
			
			if (!is_admin() && false === ( $forced_cache = get_transient( $this->transient.$sufix ) ) )
			{
				$buffer = preg_replace_callback('/(?=<div(.*?)>)(.*?)(?<=<\/div>)/s', function($matches) {
					switch($this->get_current_script($this->options))
					{
						case 'cyr_to_lat' :
							$matches[2] = $this->cyr_to_lat($matches[2]);
							break;
							
						case 'lat_to_cyr' :
							$matches[2] = $this->lat_to_cyr($matches[2]);
							break;
					}
					return $matches[2];
				}, $buffer);
				
				if(!is_admin()) set_transient( $this->transient.$sufix, $buffer, MINUTE_IN_SECONDS*3 );
			}
			else
			{
				$buffer = $forced_cache;
			}
		}
		
		return $buffer;
	}
	
	public function content ($content='') {
		if(empty($content)) return $content;
		
		switch($this->get_current_script($this->options))
		{
			case 'cyr_to_lat' :
				$content = $this->cyr_to_lat($content);
				break;
				
			case 'lat_to_cyr' :
				$content = $this->lat_to_cyr($content);			
				break;
		}
		
		return $content;
	}
	
	public function title_parts($titles=array()){
		switch($this->get_current_script($this->options))
		{
			case 'cyr_to_lat' :
				foreach($titles as $key => $val)
				{
					$titles[$key]= $this->cyr_to_lat($titles[$key]);
				}
				break;
				
			case 'lat_to_cyr' :
				foreach($titles as $key => $val)
				{
					$titles[$key]= $this->lat_to_cyr($titles[$key], true);
				}
				break;
		}
		
		return $titles;
	}
}
endif;