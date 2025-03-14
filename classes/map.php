<?php

if (!defined('WPINC')) {
    die();
}

class Transliteration_Map
{
    use Transliteration__Cache;

    /*
     * Get current instance
     */
    public static function get()
    {
        return self::cached_static('instance', fn (): \Transliteration_Map => new self(false));
    }

    /*
     * Get the current language map
     */
    public function map()
    {
        return self::cached_static('map', function () {
            // Fetch language scheme and disabled languages
            $language_scheme   = get_rstr_option('language-scheme', 'auto');
            $disable_languages = get_rstr_option('disable-by-language', []);

            // Filter disabled languages
            if (is_array($disable_languages)) {
                $disable_languages = array_keys(array_filter($disable_languages, fn ($value): bool => $value == 'yes'));
            } else {
                $disable_languages = [];
            }

            // Determine the language scheme
            if ($language_scheme === 'auto') {
                $language_scheme = Transliteration_Utilities::get_locale();
            }

            // Check if the language scheme is disabled
            if ($disable_languages && in_array($language_scheme, $disable_languages)) {
                return;
            }

            // Dynamically load the class for the language scheme
            $class = 'Transliteration_Map_' . $language_scheme;
            if (class_exists($class)) {
                return $class;
            }
        });
    }
}
