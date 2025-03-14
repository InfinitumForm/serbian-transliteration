<?php

if (!defined('WPINC')) {
    die();
}

class Transliteration_Mode_Forced extends Transliteration
{
    use Transliteration__Cache;

    // Mode ID
    public const MODE = 'forced';

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
        $this->add_action('wp_footer', 'buffer_end', 100);
    }

    /*
     * Get current instance
     */
    public static function get()
    {
        return self::cached_static('instance', fn (): \Transliteration_Mode_Forced => new self());
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
        return Transliteration_Controller::get()->transliterate($buffer);
    }

    public function buffer_end(): void
    {
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }
}
