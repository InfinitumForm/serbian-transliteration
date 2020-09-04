<?php
/**
 * Advanced Transliteration Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Mode_Advanced')) :
class Serbian_Transliteration_Mode_Advanced extends Serbian_Transliteration
{
	private $options;
	
	function __construct($options){
		$this->options = $options;
		
		$filters = array(
			'the_content' 					=> 'content',
			'the_title' 					=> 'content',
			'wp_nav_menu_items' 			=> 'content',
			'wp_title' 						=> 'content',
			'pre_get_document_title'		=> 'content',
			'default_post_metadata'			=> 'content',
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
	}
	
	public function content ($content='') {
		if(empty($content)) return $content;
		
		switch(isset($this->options['transliteration-mode']) ? $this->options['transliteration-mode'] : NULL)
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
		switch(isset($this->options['transliteration-mode']) ? $this->options['transliteration-mode'] : NULL)
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