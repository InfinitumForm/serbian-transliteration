<?php if ( !defined('WPINC') ) die();
/**
 * Active Theme: Themify
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Theme_Avada extends Transliteration
{
	function __construct(){
		$this->add_filter('transliteration_mode_filters', 'filters');
	} 
	
	public function filters ($filters=array()) {
		$filters = array_merge($filters, array(
			'avada_blog_read_more_link' => 'content',
			'avada_render_blog_post_content' => 'content',
			'avada_read_more_name' => 'content',
			'avada_before_body_content' => 'content',
			'avada_after_content' => 'content',
			'avada_after_header_wrapper' => 'content',
			'avada_after_main_container' => 'content',
			'avada_after_main_content' => 'content',
			'avada_author_info' => 'content',
			'avada_before_additional_page_content' => 'content',
			'avada_before_additional_post_content' => 'content',
			'avada_before_comments' => 'content',
			'avada_before_header_wrapper' => 'content',
			'avada_before_main_container' => 'content',
			'avada_blog_post_content' => 'content',
			'avada_blog_post_date_and_format' => 'content',
			'avada_render_blog_post_format' => 'content',
			'avada_render_blog_post_date' => 'content',
			'avada_footer_copyright_content' => 'content',
			'avada_header_inner_after' => 'content',
			'fusion_before_additional_portfolio_content' => 'content',
			'fusion_after_additional_portfolio_content' => 'content',
			'fusion_before_portfolio_side_content' => 'content',
			'fusion_after_portfolio_side_content' => 'content',
			'avada_render_footer' => 'content',
			'fusion_quick_view_summary_content' => 'content',
			'avada_admin_welcome_screen_content' => 'content',
			'fusion_portfolio_post_project_description_label' => 'content',
			'fusion_portfolio_post_project_details_label' => 'content',
			'fusion_portfolio_post_skills_label' => 'content',
			'fusion_portfolio_post_categories_label' => 'content',
			'fusion_portfolio_post_tags_label' => 'content',
			'fusion_portfolio_post_project_url_label' => 'content',
			'fusion_portfolio_post_copyright_label' => 'content',
			'fusion_portfolio_post_author_label' => 'content',
			'fusion_breadcrumbs_defaults' => 'content',
			'avada_embeds_consent_text' => 'content',
			'default_page_template_title' => 'content',
			'avada_h1_typography_elements' => 'content',
			'avada_h2_typography_elements' => 'content',
			'avada_h3_typography_elements' => 'content',
			'avada_h4_typography_elements' => 'content',
			'avada_h5_typography_elements' => 'content',
			'avada_h6_typography_elements' => 'content',
			'avada_post_title_typography_elements' => 'content',
			'avada_post_title_extras_typography_elements' => 'content',
			'avada_load_more_posts_name' => 'content',
			'avada_logo_alt_tag' => 'content',
			'fusion_faq_all_filter_name' => 'content',
			'fusion_sharing_box_tagline' => 'content',
			'fusion_post_content_is_excerpted' => 'content',
			'fusion_related_posts_heading_text' => 'content',
			'avada_secondary_header_content' => 'content',
			'tribe_events_get_the_excerpt' => 'content'
		));
		
		return $filters;
	}
	
}