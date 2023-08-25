=== Transliterator ===
Contributors: ivijanstefan, creativform
Tags: cyrillic, latin, transliteration, latinisation, serbian, latinizacija, preslovljavanje, letter, script, multilanguage, gutenberg, elementor
Requires at least: 5.4
Tested up to: 6.3
Requires PHP: 7.0
Stable tag: 1.10.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.buymeacoffee.com/ivijanstefan

Universal transliterator for permalinks, posts, tags, categories, media, files, search and more, rendering them universally readable.

== Description ==
This plugin provides a streamlined and straightforward solution for transliterating your WordPress content between Cyrillic and Latin scripts. It is designed to be lightweight and user-friendly, requiring just a few clicks to carry out the conversion. An additional feature of this plugin is its ability to support special shortcodes, allowing selective transliteration of content sections, thereby offering increased flexibility in content presentation.

= FEATURES =

 &#9989; WordPress Cyrillic to Latin and Latin to Cyrillic
 &#9989; Converts Cyrillic, European and Georgian characters in post, page and term slugs to Latin characters.
 &#9989; Transliterate Cyrillic filenames to Latin
 &#9989; Transliterate Cyrillic permalinks to Latin
 &#9989; Allow Cyrillic Usernames
 &#9989; Search posts, pages, custom post types written in cyrillic using both latin and cyrillic script
 &#9989; WP-CLI Support

= BENEFITS =

 &#9989; Compatible with Multilanguage Plugins
 &#9989; Compatible with any WordPress template
 &#9989; Compatible with SEO plugins
 &#9989; Possibility of partial transliteration
 &#9989; Scalable and customizable
 &#9989; It does not affect on the HTML, CSS or JS codes
 &#9989; Multilingual support
 &#9989; Transcription mode selection
 &#9989; Diacritical support (currently for the Serbian language)
 &#9989; Support for special characters
 &#9989; Support [PHP version 8.1](https://plugintests.com/plugins/wporg/serbian-transliteration/latest)
 &#9989; Page speed impact: [insignificant](https://plugintests.com/plugins/wporg/serbian-transliteration/latest)

= LANGUAGE SUPPORT =

 &#9989; **Serbian latinisation** - Serbian language (by locale:**sr_RS**)
 &#9989; **Bosnian latinisation** - Bosnian language (follows the rules of the Serbian language with additional special characters)
 &#9989; **Montenegrin latinisation** - Montenegrin language (follows the rules of the Serbian language with additional special characters)
 &#9989; **Russian latinisation** - Russian language (by locale:**ru_RU**)
 &#9989; **Belarusian latinisation** - Belarusian language (by locale:**bel**)
 &#9989; **Bulgarian latinisation** - Bulgarian language (by locale:**bg_BG**)
 &#9989; **Macedonian latinisation** - Macedonian language (by locale:**mk_MK**)
 &#9989; **Kazakh latinisation** - Kazakh Language (by locale:**kk**)
 &#9989; **Ukrainian latinisation** - Ukrainian Language (by locale:**uk**)
 &#9989; **Greek (Elini'ka) latinisation** - Greek Language (by locale:**el**)
 &#128312; **Arabic latinisation** - Arabic Language (EXPERIMENTAL) (by locale:**ar**)
 &#128312; **Armenian latinisation** - Armenian Language (EXPERIMENTAL) (by locale:**hy**)
 &#9989; more languages are coming soon...

= PLUGINS SUPPORT =

This plugin is made to support all known plugins and visual editors.

We also do special compatible functions with:

 &#9989; [WooCommerce](https://wordpress.org/plugins/woocommerce/)
 &#9989; [Polylang](https://wordpress.org/plugins/polylang/)
 &#9989; [Elementor Website Builder](https://wordpress.org/plugins/elementor/)
 &#9989; [CF Geo Plugin](https://wordpress.org/plugins/cf-geoplugin/)
 &#9989; [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/)
 &#9989; [Data Tables Generator by Supsystic](https://wordpress.org/plugins/data-tables-generator-by-supsystic/)
 &#9989; [Slider Revolution](https://www.sliderrevolution.com/)
 &#9989; [Avada theme](https://avada.theme-fusion.com/)
 &#9989; [Themify](https://themify.me/)
 &#9989; [Divi](https://www.elegantthemes.com/gallery/divi/) (Theme & Builder)

**It's crucial to understand** that while our plugin is compatible with most others, the sheer diversity of WordPress installations and the thousands of available plugins mean there's a small chance of encountering conflicts. We strive to ensure maximum compatibility, but given the vast number of variables, we can't guarantee flawless operation in all instances. If you experience issues, they may stem from incompatibility with other plugins. We encourage you to reach out to us or the respective plugin authors for resolution. Regularly updating all your plugins and your WordPress installation is the most common solution for most problems.

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

**Skip transliteration:**

`[rstr_skip]Keep this in original script[/rstr_skip]`

**Add an image depending on the language script:**
With this shortcode you can manipulate images and display images in Latin or Cyrillic depending on the setup.

`[rstr_img lat="YOUR_SITE_URL/logo_latin.jpg" cyr="YOUR_SITE_URL/logo_cyrillic.jpg"]`

(The documentation for these shortcodes is inside the plugin settings or see the screenshot.)

**Language script menu**
This shortcode displays a selector for the transliteration script.

`[rstr_selector]`

(The documentation for these shortcodes is inside the plugin settings or see the screenshot.)

= Available Tags =

These tags have a special purpose and work separately from short codes and can be used in fields where short codes cannot be used. These tags have no additional settings and can be applied in plugins, themes, widgets and within other short codes.

**Cyrillic to Latin:**

`{cyr_to_lat}Ћирилица у латиницу{/cyr_to_lat}`

**Latin to Cyrillic:**

`{lat_to_cyr}Latinica u ćirilicu{/lat_to_cyr}`

**Skip transliteration:**

`{rstr_skip}Keep this in original script{/rstr_skip}`

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

= 1.10.1 =
* Fixed problem with serialized data
* Fixed problems with PHP8.1 and above
* Optimized PHP code

= 1.10.0 =
* Improvement for Elementor builder
* Improvement for Oxygen builder
* Enhanced security
* Improved textdomain for translations
* Fixed bugs inside AJAX calls
* Fixed UX

= 1.9.11 =
* Added suport for Oxygen builder

= 1.9.10 =
* Removed obsolete codes

= 1.9.9 =
* Improved caching
* Support for WP version 6.3.x
* Added support for ACF and ACF PRO
* Fixed custom post type arguments

= 1.9.8 =
* Fixed a problem with the Elementor blank screen

= 1.9.7 =
* Fixed a problem with the Cyrillic version of the transliteration
* Fixed issue with JSON transliteration

= 1.9.6 =
* Fixed transliteration objects
* Introduced cached algorithms
* Removed unnecessary calling of PHP classes

= 1.9.5 =
* Fix WooCommerce transliteration problems
* Improved AJAX call transliteration

= 1.9.4 =
* Fix browser cache

= 1.9.3 =
* Added W3 Total Cache support

= 1.9.2 =
* Fixed memory leaking in WP admin
* Fixed wp admin login issue for non standard language settings

= 1.9.1 =
* Fixed fatal error on deprecated constants

= 1.9.0 =
* Added support for user locales
* Fixed languages detection

= 1.8.12 =
* Added support for the [Geo Controller](https://wordpress.org/plugins/cf-geoplugin/) plugin

= 1.8.11 =
* Fixed alternate links for SEO
* Security updates

= 1.8.10 =
* Fixed Polylang transliteration error 

= 1.8.9 =
* Improved Macedonian language
* Improved UX

= 1.8.8 =
* Fixed memory leaking in WP admin

= 1.8.7 =
* Added new skipped words for the Serbian language
* Fixed Macedonian transliteration
* Fixed locale recognation
* Improved database cache
* Optimized PHP code

= 1.8.6 =
* Fixed memory leaking on the admin pages

= 1.8.5 =
* Improved encoding

= 1.8.4 =
* Improving optimization
* Preventing memory leaks

= 1.8.3 =
* Fixed encoding
* Fixed operating system mode
* Fixed memory leaking

= 1.8.2 =
* Updated Ukrainian transliteration
* Updated Serbian transliteration
* Updated libraries
* Fixed cache algorithm

= 1.8.1 =
* Added advanced cache functionality
* Updated Ukrainian transliteration
* Optimized PHP code

= 1.8.0 =
* Added support for the WordPress version 6.0
* Update transliterations
* Update translations

= 1.7.9 =
* Fixing activation code
* Fixing translations
* Improving settings

= 1.7.8 =
* Fixing missing constants

= 1.7.7 =
* Fixed critical errors on the multisite installations

= 1.7.6 =
* Fixed iconv() PHP error
* Fixed block editor bugs

= 1.7.5 =
* Fixed mail transliterations
* Fixed file transliterations
* Improved redirection links
* Improved cache
* Improved admin settings

= 1.7.4 =
* Fixing WordPress memory leaking
* Improved cache functionality

= 1.7.3 =
* **MAJOR UPDATE:** You need to review and edit your settings
* Fixed transliteration filters
* Improved Contact Form 7 transliteration

= 1.7.2 =
* Improved cache
* Fixed object transliteration
* Adding support for the CF7

= 1.7.1 =
* Improved transliterations for the Serbian, Bosnian and montenegrin
* Improved current URL recognation
* Improved plugin cache
* Tested up to WordPress version 5.8

= 1.7.0 =
* Fixed blank screen on the Cyrillic transliteration
* Improved WP Cache object
* Removed regular expression error
* Moved to the static cache objects
* Improved AJAX calls
* Added algorithm for faster language recognition
* Removed scripts that slow down the site

== Upgrade Notice ==

= 1.10.1 =
* Fixed problem with serialized data
* Fixed problems with PHP8.1 and above
* Optimized PHP code

= 1.10.0 =
* Improvement for Elementor builder
* Improvement for Oxygen builder
* Enhanced security
* Improved textdomain for translations
* Fixed bugs inside AJAX calls
* Fixed UX

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
* Romanization of Greek
* Romanization of Arabic (EXPERIMENTAL)
* Romanization of Armenian (EXPERIMENTAL)

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

= How to transliterate Cyrillic permalinks? =
This plugin has a tool that transliterates already recorded permalinks in your database. This option is safe but requires extra effort to satisfy SEO.

With this tool, you permanently change the permalinks in your WordPress installation and a 404 error can occur if you visit old Cyrillic paths.

Therefore, you must re-asign your sitemap or make additional efforts to redirect old permalinks to new ones, which our plugin does not do.

If you are using WP-CLI, this function can also be started with a simple shell command: `wp transliterate permalinks`

= How can I define my own substitutions? =

Inside your theme's `functions.php` you can define your own substitutions for each language using filters:

`add_filter( 'rstr/inc/transliteration/{$locale}', 'function_callback', 10, 1 );`

Here's an example.

`/*
 * Modify conversion table for Serbian language.
 *
 * @param array $map Conversion map.
 *
 * @return array
 */
function my_transliteration__sr_RS( $map ) {

	// Example For 2 or more letters
	$new_map = [
		'Ња' => 'nja',
		'Ње' => 'nje',
		'Обједињени' => 'Objedinjeni'
	];
	$map = array_merge($new_map, $map);
	
	// Example for one letter
	$new_map = [
		'А'=>'X',
		'Б'=>'Y',
		'В'=>'Z'
	];
	$map = array_merge($map, $new_map);
	
	return $map;
}
add_filter( 'rstr/inc/transliteration/sr_RS', 'my_transliteration__sr_RS', 10, 1 );`

== Other Notes ==