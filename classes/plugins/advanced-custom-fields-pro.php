<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Active Plugin: Revolution Slider
 *
 * @link              http://infinitumform.com/
 * @since             1.2.4
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Advanced_Custom_Fields_Pro extends Transliteration
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
            'acf/translate_field'           => 'content',
            'acf/format_value'              => 'content',
            'acf/input/admin_l10n'          => 'content',
            'acf/taxonomy/admin_l10n'       => 'content',
            'acf/post_type/admin_l10n'      => 'content',
            'acf/fields/taxonomy/result'    => 'content',
            'acf/fields/post_object/result' => 'content',
            'acf_the_content'               => 'content',
            'acf/prepare_field'             => [self::class, 'label_attr'],
            'acf/acf_get_posts/results'     => 'the_posts_filter',
        ]);
    }

    public static function label_attr(array $field): array
    {
        $field['label'] = Transliteration_Controller::get()->transliterate_no_html($field['label']);
        return $field;
    }
}
