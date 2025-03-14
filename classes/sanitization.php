<?php

if (!defined('WPINC')) {
    die();
}

final class Transliteration_Sanitization
{
    use Transliteration__Cache;

    /*
     * Get current instance
     */
    public static function get()
    {
        return self::cached_static('instance', fn (): \Transliteration_Sanitization => new self(false));
    }

    /*
     * Fix the Latin content
     */
    public function lat($content, $sanitize_html = false)
    {
        return apply_filters('transliteration_sanitization_lat', $content, $content, $sanitize_html);
    }

    /*
     * Fix the Cyrillic content
     */
    public function cyr($content, $sanitize_html = false)
    {
        return apply_filters('transliteration_sanitization_cyr', $content, $content, $sanitize_html);
    }
}
