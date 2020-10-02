=== Transliterator - WordPress Transliteration ===
Contributors: ivijanstefan, creativform, dizajn24
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=creativform@gmail.com
Tags: cyrillic, latin, transliteration, latinisation, serbian, latinizacija, preslovljavanje, letter, script, multilanguage, gutenberg, elementor
Requires at least: 4.0
Tested up to: 5.5
Requires PHP: 7.0
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Transliterate Cyrillic into Latin, enable Cyrillic usernames, search in multiple letter scripts, and more...

== Description ==
This is a simple and easy plugin with which you can transliterate your WordPress installation from Cyrillic to Latin and vice versa in a few clicks. This transliteration also supports special shortcodes that you can use to partially transliterate parts of the content.

= FEATURES =

* WordPress Cyrillic to Latin
* Converts Cyrillic, European and Georgian characters in post, page and term slugs to Latin characters.
* Transliterate Cyrillic filenames to Latin
* Transliterate Cyrillic permalinks to Latin
* Allow Cyrillic Usernames
* Search posts, pages, custom post types written in cyrillic using both latin and cyrillic script

= BENEFITS =

* Compatible with Multilanguage Plugins
* Compatible with any WordPress template
* Possibility of partial transliteration
* Scalable and customizable
* It does not affect on the HTML, CSS or JS codes
* Multilingual support
* Transcription mode selection
* The plugin didn't make the site noticeably slower (light weight)

= LANGUAGE SUPPORT =

* **Serbian latinisation** - Serbian language (by locale:**sr_RS**)
* **Bosnian latinisation** - Bosnian language (follows the rules of the Serbian language with additional special characters)
* **Montenegrin latinisation** - Montenegrin language (follows the rules of the Serbian language with additional special characters)
* **Russian latinisation** - Russian language (by locale:**ru_RU**)
* **Belarusian latinisation** - Belarusian language (by locale:**bel**)
* **Bulgarian latinisation** - Bulgarian language (by locale:**bg_BG**)
* **Macedonian latinisation** - Macedonian language (by locale:**mk_MK**)
* **Kazakh latinisation** - Kazakh Language (by locale:**kk**)
* more languages are coming soon...

= PLUGINS SUPPORT =
This plugin is made to support all known plugins and visual editors.

**YOU NEED TO KNOW** that even if there is compatibility for most plugins, the combination of multiple plugins still has a slight chance of some conflict. There are over a couple thousand plugins and a couple of few million individual WordPress installations. We cannot 100% guarantee that everything will work properly in all possible cases. Sometimes it happens that other plugins are not compatible with this plugin, so it is important that you contact us or other plugin authors in case of any problem so that you can solve the problem. The most common fix for most issues is if you keep all the plugins and WordPress installation up to date.

== Documentation ==

Everything you need to do is to go to `Settings->Transliteration` and setup plugin according to your needs. Just follow descriptions and you will easily manage it.

= Shortcodes =
This plugin has two shortcodes that work independently of the plugin settings. These two shortcodes aim to transliterate some content. This is great if you have an article and want to display part of the text in Cyrillic, and if your entire portal is displayed in Latin.

**Cyrillic to Latin:**

`[rstr_cyr_to_lat]Ћирилица у латиницу[/rstr_cyr_to_lat]`

**Latin to Cyrillic:**

`[rstr_lat_to_cyr]Latinica u ćirilicu[/rstr_lat_to_cyr]`

**Add an image depending on the language script:**
With this shortcode you can manipulate images and display images in Latin or Cyrillic depending on the setup.

`[rstr_img lat="YOUR_SITE_URL/logo_latin.jpg" cyr="YOUR_SITE_URL/logo_cyrillic.jpg"]`

(The documentation for these shortcodes is inside the plugin settings or see the screenshot.)

**Language script menu**
This shortcode displays a selector for the transliteration script.

`[rstr_selector]`

(The documentation for these shortcodes is inside the plugin settings or see the screenshot.)

= Permalink Tool =
This tool can rename all existing Cyrillic permalinks to Latin inside database. This tool is in the configuration of this plugin.

= PHP Functions =
We also thought of PHP developers where we have enabled several useful functions that they can use within WordPress themes and plugins. The documentation for these functions is inside the plugin settings.

== Installation ==

1. Go to `WP-Admin->Plugins->Add new`, search term "WordPress Transliteration" and click on the "install" button
2. OR, upload **serbian-transliteration.zip** to `/wp-content/plugins` directory via WordPress admin panel or upload unzipped folder to your plugins folder via FTP
3. Activate the plugin through the "Plugins" menu in WordPress
4. Go to `Settings->Transliteration` to update options

== Screenshots ==

1. Transliteration settings
2. Cyrillic page before serbian transliteration
3. Latin page after serbian transliteration
4. Shortcodes
5. Automated test
6. Permalink tools
7. Available PHP Functions

== Changelog ==

= 1.1.2 =
* Important update: Fixed inline Elementor JSON settings

= 1.1.1 =
* Important update: fixed and improved WordPress search functionality

= 1.1.0 =
* Fixed issue with language session
* Optimized PHP code
* Made preparations for future versions of the plugin

= 1.0.14 =
* Added transliteration for Kazakh language
* Added update for Gutenberg block editor
* Added post type filters for permalink tool
* Added support for older WordPress version 4.0 and above (by request)

= 1.0.13 =
* Added new transliterations for widgets and archives
* Added support for the oceanwp, sydney, blocksy, colormag, phlox
* Added alternate links to transliteration versions for the better SEO

= 1.0.12 =
* Added `[rstr_selector]` shortcode for the script menu
* Fixed transliteration problems with `[rstr_cyr_to_lat]` and `[rstr_lat_to_cyr]` shortcodes

= 1.0.11 =
* Fixed problems with title and description tags
* Added array and object mode into script_selector() function
* Fixed special characters transliteration

= 1.0.10 =
* Added Woocommerce transliteration
* Added debug mode
* Prepared for the multisite
* Improved comments transliteration
* Improved RS and HR translations
* Fixed problems with array values

= 1.0.9 =
* Added new shortcode for the image controls
* Added new PHP functions
* Added new settings
* Added documentation
* Improved transliteration
* Improved JavaScript functions
* Improved transliteration quality
* Improved Search algorithm
* Fixed date transliteration
* Fixed problems with PHP7.4
* Fixed Comments transliteration

= 1.0.8 =
* PHP 7.4 support
* Improved PHP code
* Added caching to the forced transliteration

= 1.0.7 =
* New UX
* Added Bulgarian language
* Added Macedonian language
* Added new locales
* Added new schema of the letters

= 1.0.6 =
* NEW: A tool for automatic transliteration of permalink in a database
* Improved detection of Cyrillic
* Added locale libraries
* Moved to the PHP 7.x

= 1.0.5 =
* Added option to allow Cyrillic usernames
* Added RSS feed transliteration
* Added SEO transliteration
* Added support for the visual editors
* Improved page speed

= 1.0.4 =
* Fixed Latin to Cyrillic translation in pages
* Improved search functionality
* Added Latin and words, phrases, names and expressions exclude
* Improved PHP code

= 1.0.3 =
* Added support for search in Latin and Cyrillic
* Fixed bugs from the previous version

= 1.0.2 =
* FIXED: Problems with special characters and variations
* Added Russian transliteration
* Added Belarusian transliteration

= 1.0.1 =
* FIXED: Permalink transliteration
* FIXED: Sanitization of the special characters
* Added support for the other languages
* Improved page impact
* Improved PHP orientation

= 1.0.0 =
* First stable version

== Upgrade Notice ==

= 1.1.2 =
* Important update: Fixed inline Elementor JSON settings

= 1.1.1 =
* Important update: fixed and improved WordPress search functionality

= 1.1.0 =
* Fixed issue with language session
* Optimized PHP code
* Made preparations for future versions of the plugin

== Frequently Asked Questions ==

= What is Romanization or Latinisation? =
**Romanisation or Latinisation**, in linguistics, is the conversion of writing from a different writing system to the Roman (Latin) script, or a system for doing so. Methods of romanization include transliteration, for representing written text, and transcription, for representing the spoken word, and combinations of both.

= Which Romanization does this plugin support? =

This plugin supports several world letters written in Cyrillic and enables their Romanization

* Romanization of Serbian what include Bosnian and Montenegrin
* Romanization of Russian
* Romanization of Belarusian
* Romanization of Bulgarian
* Romanization of Macedonian
* Romanization of Kazakh

Each of these transliterations is created separately and follows the rules of the active language.

= Is Latin better for SEO than Cyrillic? =
According to Google documentation and discussions on forums and blogs, it is concluded that Latin is much better for SEO and it is necessary to practice Latin at least when permalinks and file names are in Latin, while the text can be in both letters but Latin is always preferred.

= Can I translate Cyrillic letters into Latin with this plugin? =
YES! Without any problems or conflicts.

= Can I translate Latin into Cyrillic with this plugin? =
YES! This plugin can translate a Latin site into Cyrillic, but this is not recommended and often causes problems. It is suggested that this approach be approached experimentally.

== Other Notes ==

== DONATION ==

Enjoy using *WordPress Transliteration*? Please consider [making a small donation](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=creativform@gmail.com) to support the project's continued development.