<?php

if (!defined('WPINC')) {
    die();
}

class Transliteration_Blocks extends Transliteration
{
    public function __construct()
    {
        $this->add_action('init', 'register_script_selector_block');
    }

    public function register_script_selector_block(): void
    {
        $min = defined('RSTR_DEV_MODE') && RSTR_DEV_MODE ? '' : '.min';

        wp_register_script(
            'rstr-script-selector-block',
            RSTR_ASSETS . '/js/script-selector-block' . $min . '.js',
            ['wp-blocks', 'wp-element', 'wp-editor'],
            RSTR_VERSION,
            true
        );

        register_block_type('serbian-transliteration/script-selector', [
            'editor_script'   => 'rstr-script-selector-block',
            'render_callback' => [$this, 'render_script_selector_block'],
            'attributes'      => [
                'displayType' => [
                    'type'    => 'string',
                    'default' => 'inline',
                ],
            ],
        ]);
    }

    public function render_script_selector_block($attributes): string
    {
        $display_type = isset($attributes['displayType']) ? $attributes['displayType'] : 'inline';

        return script_selector([
            'display_type' => $display_type,
            'echo'         => false,
        ]);
    }
}
