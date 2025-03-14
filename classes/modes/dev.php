<?php

if (!defined('WPINC')) {
    die();
}

class Transliteration_Mode_Dev extends Transliteration
{
    use Transliteration__Cache;

    // Mode ID
    public const MODE = 'dev';

    /*
     * The main constructor
     */
    public function __construct()
    {
        $this->init_actions();
    }

    /*
     * Initialize actions (to be called only once)
     */
    public function init_actions(): void
    {
        $this->add_action('template_redirect', 'buffer_start', 1);
        $this->add_action('wp_footer', 'buffer_end', ceil(PHP_INT_MAX / 2));
    }

    /*
     * Get current instance
     */
    public static function get()
    {
        return self::cached_static('instance', fn (): \Transliteration_Mode_Dev => new self());
    }

    /*
     * Get available filters for this mode
     */
    public function filters(): array
    {
        return [];
    }

    public function buffer_start(): void
    {
        $this->ob_start('buffer_callback');
    }

    public function buffer_callback($buffer)
    {
        return $this->transliterateHTML($buffer);
    }

    public function buffer_end(): void
    {
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    public function transliterateHTML($html): string|false
    {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $skipTags = apply_filters('transliteration_html_avoid_tags', [
            'script',
            'style',
            'textarea',
            'input',
            'select',
            'code',
            'pre',
            'img',
            'svg',
            'image',
        ]);

        $attributesToTransliterate = apply_filters('transliteration_html_attributes', [
            'title',
            'data-title',
            'alt',
            'placeholder',
            'data-placeholder',
            'aria-label',
            'data-label',
            'data-description',
        ], 'inherit');

        foreach ($xpath->query('//text()') as $textNode) {
            if (!in_array($textNode->parentNode->nodeName, $skipTags)) {
                $textNode->nodeValue = Transliteration_Controller::get()->transliterate_no_html($textNode->nodeValue);
            }
        }

        foreach ($xpath->query('//*[@' . implode(' or @', $attributesToTransliterate) . ']') as $node) {
            foreach ($attributesToTransliterate as $attr) {
                if ($node->hasAttribute($attr)) {
                    $node->setAttribute($attr, Transliteration_Controller::get()->transliterate_no_html($node->getAttribute($attr)));
                }
            }
        }

        return $dom->saveHTML();
    }
}
