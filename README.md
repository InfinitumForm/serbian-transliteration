Transliteration – WordPress Transliteration
========

This is a simple and easy plugin with which you can translate your WordPress installation from Cyrillic to Latin and vice versa in a few clicks. This transliteration also supports special shortcodes that you can use to partially translate parts of the content.

## FEATURES

* WordPress Cyrillic to Latin
* Converts Cyrillic, European and Georgian characters in post, page and term slugs to Latin characters.
* Transliterate Cyrillic filenames to Latin
* Transliterate Cyrillic permalinks to Latin
* Allow Cyrillic Usernames
* Search posts, pages, custom post types written in cyrillic using both latin and cyrillic script

## BENEFITS

* Compatible with Multilanguage Plugins
* Compatible with any WordPress template
* Possibility of partial transliteration
* Scalable and customizable
* It does not affect on the HTML, CSS or JS codes
* Multilingual support
* Transcription mode selection
* Support [PHP version 7.4.8](https://plugintests.com/plugins/wporg/serbian-transliteration/latest)
* Page speed impact: [insignificant](https://plugintests.com/plugins/wporg/serbian-transliteration/latest)

## LANGUAGE SUPPORT

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

## PLUGINS SUPPORT
This plugin is made to support all known plugins and visual editors.

We also do special compatible functions with:

* [WooCommerce](https://wordpress.org/plugins/woocommerce/)
* [Polylang](https://wordpress.org/plugins/polylang/)
* [Elementor Website Builder](https://wordpress.org/plugins/elementor/)
* [WordPress Geo Plugin](https://wordpress.org/plugins/cf-geoplugin/)

**YOU NEED TO KNOW** that even if there is compatibility for most plugins, the combination of multiple plugins still has a slight chance of some conflict. There are over a couple thousand plugins and a couple of few million individual WordPress installations. We cannot 100% guarantee that everything will work properly in all possible cases. Sometimes it happens that other plugins are not compatible with this plugin, so it is important that you contact us or other plugin authors in case of any problem so that you can solve the problem. The most common fix for most issues is if you keep all the plugins and WordPress installation up to date.

This plugin can also serve as an alternative to [SrbTransLatin](https://wordpress.org/plugins/srbtranslatin/), [Cyr-To-Lat](https://wordpress.org/plugins/cyr2lat/), [Allow Cyrillic Usernames](https://wordpress.org/plugins/allow-cyrillic-usernames/), [Filenames to latin](https://wordpress.org/plugins/filenames-to-latin/), [Cyrillic Permalinks](https://wordpress.org/plugins/cyrillic-slugs/) and other similar plugins. We have managed to combine all the necessary functionalities into one plugin, but if you want to have all the separate functions, we invite you to use some of these excellent plugins.

It is important for you to know that any functionality in our plugin can be turned off if you do not need it, as well as the ability to filter certain hooks and filters. We have tried to provide maximum flexibility and compatibility to everyone.

# Documentation

Everything you need to do is to go to `Settings->Transliteration` and setup plugin according to your needs. Just follow descriptions and you will easily manage it.

## Shortcodes
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

## Permalink Tool
This tool can rename all existing Cyrillic permalinks to Latin inside the database. This tool is in the configuration of this plugin.

## PHP Functions
We also thought of PHP developers where we have enabled several useful functions that they can use within WordPress themes and plugins. The documentation for these functions is inside the plugin settings.
