<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Active Plugin: WooCommerce
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Wordpress_Seo extends Transliteration
{
    public function __construct()
    {
        $this->add_filter('transliteration_mode_filters', 'filters');
    }

    /**
     * @return mixed[]
     */
    public function filters($filters = []): array
    {
        return array_merge($filters, [
            'wpseo_breadcrumb_links'    => 'content',
            'wpseo_title'               => 'content',
            'wpseo_robots'              => 'content',
            'wpseo_metakey'             => 'content',
            'wpseo_metadesc'            => 'content',
            'wpseo_metakeywords'        => 'content',
            'wpseo_twitter_description' => 'content',
            'wpseo_twitter_title'       => 'content',
            'wpseo_opengraph_title'     => 'content',
            'wpseo_html_namespaces'     => 'content',
        ]);
    }
}
