<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Active Plugin: WPBakery Page Builder (Visual Composer)
 *
 * @link              http://infinitumform.com/
 * @since             2.3.3
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Js_Composer extends Transliteration
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
            // Common output filters used by WPBakery
            'vc_gitem_template_attribute_text'   => 'content',
            'vc_gitem_template_attribute_title'  => 'content',
            'vc_gitem_template_attribute_content'=> 'content',
        ]);
    }
}
