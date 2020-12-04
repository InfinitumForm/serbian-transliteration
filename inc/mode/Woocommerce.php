<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Woocommerce (Deprecated mode)
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 * @contributor       Slobodan Pantovic
 */
if(!class_exists('Serbian_Transliteration_Mode_Standard')) :
	class Serbian_Transliteration_Mode_Woocommerce extends Serbian_Transliteration
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
			
			$filters = array();
			
			return $filters;
		}

		function __construct($options){
			if($options !== false)
			{
				$this->options = $options;
				
				$filters = self::filters($this->options);
				$filters = apply_filters('rstr/transliteration/exclude/filters', $filters, $this->options);

				if(!is_admin())
				{
					foreach($filters as $filter=>$function) $this->add_filter($filter, $function, 9999999, 1);
				}
			}
		}
	}
endif;