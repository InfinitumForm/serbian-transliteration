<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Plugin: WooCommerce
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Plugin__wordpress_seo')) :
	class Serbian_Transliteration__Plugin__wordpress_seo extends Serbian_Transliteration
	{

		/* Run this script */
		public static function run($dry = false) {
			$class = self::class;
			$instance = Serbian_Transliteration_Cache::get($class);
			if ( !$instance ) {
				$instance = Serbian_Transliteration_Cache::set($class, new self($dry));
			}
			return $instance;
		}

		function __construct($dry = false){
			if($dry) return;
			$this->add_filter('rstr/transliteration/exclude/filters', array(get_class(), 'filters'));
		}

		public static function filters ($filters=array()) {

			$classname = self::run(true);
			$filters = array_merge($filters, array(
				'wpseo_breadcrumb_links' => array($classname, 'content'),
				'wpseo_title' => array($classname, 'content'),
				'wpseo_robots' => array($classname, 'content'),
				'wpseo_metakey' => array($classname, 'content'),
				'wpseo_metadesc' => array($classname, 'content'),
				'wpseo_metakeywords' => array($classname, 'content'),
				'wpseo_twitter_description' => array($classname, 'content'),
				'wpseo_twitter_title' => array($classname, 'content'),
				'wpseo_opengraph_title' => array($classname, 'content'),
				'wpseo_html_namespaces' => array($classname, 'content')
			));
			asort($filters);
			return $filters;
		}

		public function content ($content='') {
			if(empty($content)) return $content;


			if(is_array($content))
			{
				if(method_exists($this, 'transliterate_objects')) {
					$content = $this->transliterate_objects($content, false);
				}
			}
			else if(is_string($content))
			{

				if(method_exists($this, 'transliterate_text')) {
					$content = $this->transliterate_text($content, false);
				}
			}
			return $content;
		}

	}
endif;
