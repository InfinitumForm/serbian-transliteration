<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Active Plugin: Elementor Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Elementor extends Transliteration
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
            'elementor/frontend/the_content' => 'content',
        ]);
    }
}
