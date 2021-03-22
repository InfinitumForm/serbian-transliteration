<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Active Theme: Themify
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration__Theme__avada')) :
	class Serbian_Transliteration__Theme__avada extends Serbian_Transliteration
	{
		
		/* Run this script */
		public static function run($dry = false) {
			global $rstr_cache;
			$class = self::class;
			$instance = $rstr_cache->get($class);
			if ( !$instance ) {
				$instance = $rstr_cache->set($class, new self($dry));
			}
			return $instance;
		}
		
		function __construct($dry = array()){
			if($dry) return;
			$this->add_filter('rstr/transliteration/exclude/filters', array(get_class(), 'filters'));
		} 
		
		public static function filters ($filters=array()) {

			$classname = self::run(true);
			$filters = array_merge($filters, array(
				'avada_before_body_content' => array($classname, 'content'),
				'avada_after_content' => array($classname, 'content'),
				'avada_after_header_wrapper' => array($classname, 'content'),
				'avada_after_main_container' => array($classname, 'content'),
				'avada_after_main_content' => array($classname, 'content'),
				'avada_author_info' => array($classname, 'content'),
				'avada_before_additional_page_content' => array($classname, 'content'),
				'avada_before_additional_post_content' => array($classname, 'content'),
				'avada_before_comments' => array($classname, 'content'),
				'avada_before_header_wrapper' => array($classname, 'content'),
				'avada_before_main_container' => array($classname, 'content'),
				'avada_blog_post_content' => array($classname, 'content'),
				'avada_blog_post_date_and_format' => array($classname, 'content'),
				'avada_footer_copyright_content' => array($classname, 'content'),
				'avada_header_inner_after' => array($classname, 'content'),
				'fusion_before_additional_portfolio_content' => array($classname, 'content'),
				'fusion_after_additional_portfolio_content' => array($classname, 'content'),
				'fusion_before_portfolio_side_content' => array($classname, 'content'),
				'fusion_after_portfolio_side_content' => array($classname, 'content'),
				'avada_render_footer' => array($classname, 'content'),
				'fusion_quick_view_summary_content' => array($classname, 'content'),
				'avada_admin_welcome_screen_content' => array($classname, 'content'),
				'fusion_portfolio_post_project_description_label' => array($classname, 'content'),
				'fusion_portfolio_post_project_details_label' => array($classname, 'content'),
				'fusion_portfolio_post_skills_label' => array($classname, 'content'),
				'fusion_portfolio_post_categories_label' => array($classname, 'content'),
				'fusion_portfolio_post_tags_label' => array($classname, 'content'),
				'fusion_portfolio_post_project_url_label' => array($classname, 'content'),
				'fusion_portfolio_post_copyright_label' => array($classname, 'content'),
				'fusion_portfolio_post_author_label' => array($classname, 'content'),
				'fusion_breadcrumbs_defaults' => array($classname, 'content'),
				'avada_embeds_consent_text' => array($classname, 'content'),
				'avada_h1_typography_elements' => array($classname, 'content'),
				'avada_h2_typography_elements' => array($classname, 'content'),
				'avada_h3_typography_elements' => array($classname, 'content'),
				'avada_h4_typography_elements' => array($classname, 'content'),
				'avada_h5_typography_elements' => array($classname, 'content'),
				'avada_h6_typography_elements' => array($classname, 'content'),
				'avada_post_title_typography_elements' => array($classname, 'content'),
				'avada_post_title_extras_typography_elements' => array($classname, 'content'),
				'avada_load_more_posts_name' => array($classname, 'content'),
				'avada_logo_alt_tag' => array($classname, 'content'),
				'fusion_faq_all_filter_name' => array($classname, 'content'),
				'fusion_sharing_box_tagline' => array($classname, 'content')
			));
			
			return $filters;
		}
		
		public function content ($content='') {
			if(empty($content)) return $content;
			
			
			if(is_array($content))
			{
				if(method_exists($this, 'transliterate_objects')) {
					$content = $this->transliterate_objects($content);
				}
			}
			else if(is_string($content) && !is_numeric($content))
			{
					
				switch($this->get_current_script($this->get_options()))
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