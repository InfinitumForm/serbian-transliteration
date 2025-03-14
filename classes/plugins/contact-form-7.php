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

class Transliteration_Plugin_Contact_Form_7 extends Transliteration
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
        //	'wpcf7_display_message' => 'content',
        //	'wpcf7_default_template' => 'content',
        //	'wpcf7_form_response_output' => 'content',
        //	'wpcf7_ajax_json_echo' => 'content',
        //	'wpcf7_ajax_onload' => 'content',
        //	'wpcf7_contact_form_shortcode' => 'content',
            'wpcf7_flamingo_get_value' => 'content',
        //	'wpcf7_form_autocomplete' => 'content',
            'wpcf7_form_tag'         => [self::class, 'tag_handler'],
            'wpcf7_messages'         => [self::class, 'transliterate_messages'],
            'wpcf7_validation_error' => [self::class, 'validation_error'],
        ]);
    }

    public static function tag_handler(array $tag, $unused = ''): array
    {
        if ($tag['type'] === 'text') {
            $tag['name'] = Transliteration_Mode::get()->content($tag['name']);
        }

        return $tag;
    }

    public static function validation_error($error, $tag = '')
    {
        if (!empty($error)) {
            return Transliteration_Mode::get()->content($error);
        }

        return $error;
    }

    public static function transliterate_messages(array $messages): array
    {
        $transliteration = Transliteration_Mode::get();

        foreach ($messages as $key => $message) {
            if (isset($message['default'])) {
                $messages[$key]['default'] = $transliteration->content($message['default']);
            }

            if (isset($message['description'])) {
                $messages[$key]['description'] = $transliteration->content($message['description']);
            }
        }

        return $messages;
    }
}
