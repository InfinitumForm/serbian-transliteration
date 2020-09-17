<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Standard Transliteration Mode
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Mode_Standard')) :
class Serbian_Transliteration_Mode_Standard extends Serbian_Transliteration
{
	private $options;

	function __construct($options){
		$this->options = $options;
		
		$filters = array(
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
			'gettext' 				=> 'content',
			'ngettext' 				=> 'content',
			'gettext_with_context' 	=> 'content',
			'ngettext_with_context' => 'content',
			'document_title_parts' 	=> 'title_parts'
		);
		
		if (!current_theme_supports( 'title-tag' )){
			unset($filters['document_title_parts']);
			unset($filters['pre_get_document_title']);
		} else {
			unset($filters['wp_title']);
		}
		
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