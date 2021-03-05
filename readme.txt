=== Transliterator - WordPress Transliteration ===
Contributors: ivijanstefan, creativform, dizajn24, tihi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=creativform@gmail.com
Tags: cyrillic, latin, transliteration, latinisation, serbian, latinizacija, preslovljavanje, letter, script, multilanguage, gutenberg, elementor
Requires at least: 5.4
Tested up to: 5.7
Requires PHP: 7.0
Stable tag: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Transliterate Cyrillic into Latin, enable Cyrillic usernames, search in multiple letter scripts, and more...

== Description ==
This is a light weight, simple and easy plugin with which you can transliterate your WordPress installation from Cyrillic to Latin and vice versa in a few clicks. This transliteration plugin also supports special shortcodes that you can use to partially transliterate parts of the content.

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
* Compatible with SEO plugins
* Possibility of partial transliteration
* Scalable and customizable
* It does not affect on the HTML, CSS or JS codes
* Multilingual support
* Transcription mode selection
* Diacritical support (currently for the Serbian language)
* Support [PHP version 7.4.8](https://plugintests.com/plugins/wporg/serbian-transliteration/latest)
* Page speed impact: [insignificant](https://plugintests.com/plugins/wporg/serbian-transliteration/latest)

= LANGUAGE SUPPORT =

* **Serbian latinisation** - Serbian language (by locale:**sr_RS**)
* **Bosnian latinisation** - Bosnian language (follows the rules of the Serbian language with additional special characters)
* **Montenegrin latinisation** - Montenegrin language (follows the rules of the Serbian language with additional special characters)
* **Russian latinisation** - Russian language (by locale:**ru_RU**)
* **Belarusian latinisation** - Belarusian language (by locale:**bel**)
* **Bulgarian latinisation** - Bulgarian language (by locale:**bg_BG**)
* **Macedonian latinisation** - Macedonian language (by locale:**mk_MK**)
* **Kazakh latinisation** - Kazakh Language (by locale:**kk**)
* **Ukrainian latinisation** - Ukrainian Language (by locale:**uk**)
* more languages are coming soon...

= PLUGINS SUPPORT =

This plugin is made to support all known plugins and visual editors.

We also do special compatible functions with:

* [WooCommerce](https://wordpress.org/plugins/woocommerce/)
* [Polylang](https://wordpress.org/plugins/polylang/)
* [Elementor Website Builder](https://wordpress.org/plugins/elementor/)
* [WordPress Geo Plugin](https://wordpress.org/plugins/cf-geoplugin/)
* [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/)

**YOU NEED TO KNOW** that even if there is compatibility for most plugins, the combination of multiple plugins still has a slight chance of some conflict. There are over a couple thousand plugins and a couple of few million individual WordPress installations. We cannot 100% guarantee that everything will work properly in all possible cases. Sometimes it happens that other plugins are not compatible with this plugin, so it is important that you contact us or other plugin authors in case of any problem so that you can solve the problem. The most common fix for most issues is if you keep all the plugins and WordPress installation up to date.

This plugin can also serve as an alternative to [SrbTransLatin](https://wordpress.org/plugins/srbtranslatin/), [Cyr-To-Lat](https://wordpress.org/plugins/cyr2lat/), [Allow Cyrillic Usernames](https://wordpress.org/plugins/allow-cyrillic-usernames/), [Filenames to latin](https://wordpress.org/plugins/filenames-to-latin/), [Cyrillic Permalinks](https://wordpress.org/plugins/cyrillic-slugs/) and other similar plugins. We have managed to combine all the necessary functionalities into one plugin, but if you want to have all the separate functions, we invite you to use some of these excellent plugins.

It is important for you to know that any functionality in our plugin can be turned off if you do not need it, as well as the ability to filter certain hooks and filters. We have tried to provide maximum flexibility and compatibility to everyone.

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
This tool can rename all existing Cyrillic permalinks to Latin inside the database. This tool is in the configuration of this plugin.

= PHP Functions =
We also thought of PHP developers where we have enabled several useful functions that they can use within WordPress themes and plugins. The documentation for these functions is inside the plugin settings.

== Installation ==

1. Go to `WP-Admin->Plugins->Add new`, search term "WordPress Transliteration" and click on the "install" button
2. OR, upload **serbian-transliteration.zip** to `/wp-content/plugins` directory via WordPress admin panel or upload unzipped folder to your plugins folder via FTP
3. Activate the plugin through the "Plugins" menu in WordPress
4. Go to `Settings->Transliteration` to update options

== Screenshots ==

1. Cyrillic page before serbian transliteration
2. Latin page after serbian transliteration
3. Transliteration settings
4. Converter for transliterating Cyrillic into Latin and vice versa
5. Permalink tools
6. Shortcodes
7. Available PHP Functions
8. Language script inside Menus
9. Automated test

== Changelog ==

= 1.4.4 =
* Add special transliteration tags
* Corrected navigation transliteration
* Advanced memory storage algorithms

= 1.4.3 =
* **MAJOR UPDATE:** You need to review and edit your settings
* Added manual language script scheme selection
* Added script for Montenegrin characters
* Added WP-CLI script for permalink transliteration: `wp transliterate permalinks`
* Optimized scripts for libraries
* Add new filters for Yoast SEO
* Added support for WordPress version 5.7

= 1.4.2 =
* Fixed issue with `data-*` attributes
* Fixed issue with JSON inside attributes
* Fixed problem of Latin to Cyrillic transliteration (extended characters)
* Advanced algorithm to support Elementor and Visual Composer plugins

= 1.4.1 =
* Improved internal caching functionality
* Improved PHP code and optimized memory
* Page acceleration

= 1.4.0 =
* **MAJOR UPDATE:** You need to review and edit your settings
* Improved transliteration of Latin into Cyrillic
* Improved HTML parsing
* Improved PHP functions for developers
* Improved shortcodes
* Improved menu navigation for language script
* Improved cache cleanup when changing script
* Optimized PHP code

== Upgrade Notice ==

= 1.4.4 =
* Add special transliteration tags
* Corrected navigation transliteration
* Advanced memory storage algorithms

= 1.4.3 =
* **MAJOR UPDATE:** You need to review and edit your settings
* Added manual language script scheme selection
* Added script for Montenegrin characters
* Added WP-CLI script for permalink transliteration: `wp transliterate permalinks`
* Optimized scripts for libraries
* Add new filters for Yoast SEO
* Added support for WordPress version 5.7

= 1.4.2 =
* Fixed issue with `data-*` attributes
* Fixed issue with JSON inside attributes
* Fixed problem of Latin to Cyrillic transliteration (extended characters)
* Advanced algorithm to support Elementor and Visual Composer plugins

= 1.4.1 =
* Improved internal caching functionality
* Improved PHP code and optimized memory
* Page acceleration

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
* Romanization of Ukrainian

Each of these transliterations is created separately and follows the rules of the active language.

= What is the best practice for transliteration? =
Through various experiences, we came to the conclusion that it is best to create the entire site in Cyrillic and enable transliteration for Latin.

The reason for this solution lies in the problem of transliteration of Latin into Cyrillic due to encoding and, depending on the server, can create certain problems, especially in communication with the database. Creating a site in Cyrillic bypasses all problems and is very easily translated into Latin.

= Is Latin better for SEO than Cyrillic? =
According to Google documentation and discussions on forums and blogs, it is concluded that Latin is much better for SEO and it is necessary to practice Latin at least when permalinks and file names are in Latin, while the text can be in both letters but Latin is always preferred.

= Can I translate Cyrillic letters into Latin with this plugin? =
YES! Without any problems or conflicts.

= Can I translate Latin into Cyrillic with this plugin? =
YES! This plugin can translate a Latin site into Cyrillic, but this is not recommended and often causes problems. It is suggested that this approach be approached experimentally.

The best practice is to create a Cyrillic site including all other content and in the end just add transliteration to navigation so that the visitor can choose the desired script.

== Other Notes ==