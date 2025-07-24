<?php

if (!defined('WPINC')) {
    die();
}

class Transliteration_Blocks extends Transliteration
{
	private $lat;
	private $cyr;
	
    public function __construct()
    {
        $this->add_action('init', 'register_script_selector_block');
    }
	
	private function load_titles() {
		if(!$this->lat) {
			$this->lat = '{cyr_to_lat}' . _x('Latin', 'Block Editor: Script selector', 'serbian-transliteration') . '{/cyr_to_lat}';
		}
		if(!$this->cyr) {
			$this->cyr = '{lat_to_cyr}' . _x('Cyrillic', 'Block Editor: Script selector', 'serbian-transliteration') . '{/lat_to_cyr}';
		}
	}

    public function register_script_selector_block(): void
	{
		$this->load_titles();
		
		$min = defined('RSTR_DEV_MODE') && RSTR_DEV_MODE ? '' : '.min';

		wp_register_script(
			'rstr-script-selector-block',
			RSTR_ASSETS . '/js/script-selector-block' . $min . '.js',
			['wp-blocks', 'wp-element', 'wp-editor'],
			(empty($min) ? RSTR_ROOT.'/assets/js/script-selector-block.js' : RSTR_VERSION),
			true
		);

		// Prosleđivanje parametara iz PHP u JS
		wp_localize_script('rstr-script-selector-block', 'rstr_block_settings', [
			'labels' => [
				'display_type' => _x('Choose your display type:', 'Block Editor: Script selector', 'serbian-transliteration'),
				'script_selector' => [
					'title' => _x('Script Selector', 'Block Editor: Script selector', 'serbian-transliteration'),
					'option' => [
						_x('Inline', 'Block Editor: Script selector', 'serbian-transliteration'),
						_x('Select', 'Block Editor: Script selector', 'serbian-transliteration'),
						_x('List', 'Block Editor: Script selector', 'serbian-transliteration'),
						_x('List items', 'Block Editor: Script selector', 'serbian-transliteration')
					]
				],
				'separator' => _x('Separator:', 'Block Editor: Script selector', 'serbian-transliteration'),
				'cyrillic_caption' => _x('Cyrillic caption:', 'Block Editor: Script selector', 'serbian-transliteration'),
				'latin_caption' => _x('Latin Caption:', 'Block Editor: Script selector', 'serbian-transliteration'),
				'cyrillic' => $this->cyr,
				'latin' => $this->lat
			],
		]);

		register_block_type('serbian-transliteration/script-selector', [
			'editor_script'   => 'rstr-script-selector-block',
			'editor_style'    => 'rstr-script-selector-editor-style',
			'render_callback' => [$this, 'render_script_selector_block'],
			'attributes'      => [
				'displayType' => [
					'type'    => 'string',
					'default' => 'inline',
				],
				'separator' => [
					'type'    => 'string',
					'default' => '|',
				],
				'cyr_caption' => [
					'type'    => 'string',
					'default' => $this->cyr,
				],
				'lat_caption' => [
					'type'    => 'string',
					'default' => $this->lat,
				],
			],
		]);
		
		// Register editor style
		wp_register_style(
			'rstr-script-selector-editor-style',
			RSTR_ASSETS . '/css/script-selector-block-editor' . $min . '.css',
			['wp-edit-blocks'],
			(empty($min) ? RSTR_ROOT.'/assets/css/script-selector-block-editor.css' : RSTR_VERSION)
		);
	}

    public function render_script_selector_block($attributes): string
    {
		$this->load_titles();
	
        $display_type = isset($attributes['displayType']) ? $attributes['displayType'] : 'inline';

        return script_selector([
			'display_type' => $attributes['displayType'] ?? 'inline',
			'separator'    => $attributes['separator'] ?? '|',
			'cyr_caption'  => $attributes['cyr_caption'] ?? $this->cyr,
			'lat_caption'  => $attributes['lat_caption'] ?? $this->lat,
			'echo'         => false,
		]);
    }
}
