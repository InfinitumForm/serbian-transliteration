<?php

if (!defined('WPINC')) {
    die();
}

class Transliteration_Mode_Woocommerce
{
    use Transliteration__Cache;

    // Mode ID
    public const MODE = 'woocommerce';

    /*
     * Get current instance
     */
    public static function get()
    {
        return self::cached_static('instance', fn (): \Transliteration_Mode_Woocommerce => new self());
    }

    /*
     * Get available filters for this mode
     */
    public function filters(): array
    {
        return [];
    }
}
