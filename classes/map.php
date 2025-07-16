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
	
	
	public static function transliterate(string $text, string $direction): string
	{
		$map = self::get_map($direction);
		if (!$map) {
			return $text;
		}

		// Sort longer keys first (e.g. 'dž' before 'd') to avoid partial matches
		uksort($map, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));

		// Escape keys for regex
		$escapedKeys = array_map('preg_quote', array_keys($map));
		$pattern = '/' . implode('|', $escapedKeys) . '/iu';

		return preg_replace_callback($pattern, function ($matches) use ($map) {
			$match = $matches[0];

			// Match both uppercase and lowercase entries
			foreach ($map as $latin => $cyrillic) {
				if (strcasecmp($match, $latin) === 0) {
					// Preserve case
					if (ctype_upper($match)) {
						return mb_strtoupper($cyrillic);
					} elseif (preg_match('/^\p{Lu}\p{Ll}+$/u', $match)) {
						return mb_strtoupper(mb_substr($cyrillic, 0, 1)) . mb_substr($cyrillic, 1);
					}

					return $cyrillic;
				}
			}

			return $match;
		}, $text);
	}
}
