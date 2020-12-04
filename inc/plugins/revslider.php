<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Plugin__revslider')) :
	class Serbian_Transliteration__Plugin__revslider extends Serbian_Transliteration
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
			
			$classname = self::run(false);
			$filters = array_merge($filters, array(
				'revslider_add_static_layer_html' => array($classname, 'content'),
				'revslider_mod_stream_meta' => array($classname, 'content'),
				'revslider_add_layer_html' => array($classname, 'content')
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