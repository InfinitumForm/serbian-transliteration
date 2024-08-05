<?php if ( !defined('WPINC') ) die();
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
				'wpseo_breadcrumb_links' => 'content',
				'wpseo_title' => 'content',
				'wpseo_robots' => 'content',
				'wpseo_metakey' => 'content',
				'wpseo_metadesc' => 'content',
				'wpseo_metakeywords' => 'content',
				'wpseo_twitter_description' => 'content',
				'wpseo_twitter_title' => 'content',
				'wpseo_opengraph_title' => 'content',
				'wpseo_html_namespaces' => 'content'
			));
			asort($filters);
			return $filters;
		}
	}
endif;
