<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Theme: Themify
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Theme__themify')) :
	class Serbian_Transliteration__Theme__themify extends Serbian_Transliteration
	{
		private $options;
		
		/* Run this script */
		private static $__run = NULL;
		public static function run($options = array()) {
			if( !self::$__run ) self::$__run = new self($options);
			return self::$__run;
		}
		
		function __construct($options = array()){
			if($options !== false)
			{
				$this->options = $options;
				
				$this->add_filter('rstr/transliteration/exclude/filters', array(get_class(), 'filters'));
			}
		} 
		
		public static function filters ($filters=array()) {

			$classname = get_class();
			$filters = array_merge($filters, array(
				'tf_hook_body_start' => array($classname, 'content'),
				'tf_hook_header_before' => array($classname, 'content'),
				'tf_hook_header_start' => array($classname, 'content'),
				'tf_hook_header_end' => array($classname, 'content'),
				'tf_hook_header_after' => array($classname, 'content'),
				'tf_hook_layout_before' => array($classname, 'content'),
				'tf_hook_content_before' => array($classname, 'content'),
				'tf_hook_content_start' => array($classname, 'content'),
				'tf_hook_post_before' => array($classname, 'content'),
				'tf_hook_post_start' => array($classname, 'content'),
				'tf_hook_post_end' => array($classname, 'content'),
				'tf_hook_post_after' => array($classname, 'content'),
				'tf_hook_comment_before' => array($classname, 'content'),
				'tf_hook_comment_start' => array($classname, 'content'),
				'tf_hook_commentform_before' => array($classname, 'content'),
				'tf_hook_commentform_start' => array($classname, 'content'),
				'tf_hook_commentform_end' => array($classname, 'content'),
				'tf_hook_commentform_after' => array($classname, 'content'),
				'tf_hook_comment_end' => array($classname, 'content'),
				'tf_hook_comment_after' => array($classname, 'content'),
				'tf_hook_content_end' => array($classname, 'content'),
				'tf_hook_content_after' => array($classname, 'content'),
				'tf_hook_sidebar_before' => array($classname, 'content'),
				'tf_hook_sidebar_start' => array($classname, 'content'),
				'tf_hook_sidebar_end' => array($classname, 'content'),
				'tf_hook_sidebar_after' => array($classname, 'content'),
				'tf_hook_layout_after' => array($classname, 'content'),
				'tf_hook_footer_before' => array($classname, 'content'),
				'tf_hook_footer_start' => array($classname, 'content'),
				'tf_hook_footer_end' => array($classname, 'content'),
				'tf_hook_footer_after' => array($classname, 'content'),
				'tf_hook_body_end' => array($classname, 'content')
			));
			
			return $filters;
		}
		
		public function content ($content='') {
			if(empty($content)) return $content;
			
			
			if(is_array($content))
			{
				$content = $this->title_parts($content);
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
		
		public function title_parts($titles=array()){
			switch($this->get_current_script($this->options))
			{
				case 'cyr_to_lat' :
					foreach($titles as $key => $val)
					{
						if(is_string($val) && !is_numeric($val)) $titles[$key]= $this->cyr_to_lat($titles[$key]);
					}
					break;
					
				case 'lat_to_cyr' :
					foreach($titles as $key => $val)
					{
						if(is_string($val) && !is_numeric($val)) $titles[$key]= $this->lat_to_cyr($titles[$key], true);
					}
					break;
			}
			
			return $titles;
		}
	}
endif;