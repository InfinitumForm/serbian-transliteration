=== Transliterator ===
Contributors: ivijanstefan, creativform, tihi
Tags: cyrillic, latin, transliteration, latinisation, cyr2lat
Requires at least: 5.4
Tested up to: 6.6
Requires PHP: 7.0
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.buymeacoffee.com/ivijanstefan

Universal transliterator for permalinks, posts, tags, categories, media, files, search and more, rendering them universally readable.

== Description ==
This plugin provides a seamless solution for converting WordPress content between Cyrillic and Latin scripts. Originally starting as "Serbian Transliteration", it has evolved into a general "Transliterator" for WordPress, crafted to be user-friendly and lightweight. [Learn more about its transformation](https://buymeacoffee.com/ivijanstefan/transliterator-wordpress-transforming-language-barriers-bridges) from a simple Serbian tool to a versatile transliteration plugin. The Transliterator plugin facilitates the conversion process with minimal clicks and supports unique shortcodes, enabling selective transliteration of designated content sections, delivering superior flexibility in how content is presented. Whether you want to transliterate entire pages, specific parts of your content, or even permalinks, this plugin gives you full control. Make your site Latin now!

Just go to `Settings->Transliteration` and configure the plugin according to your needs.

**Key Features:**
- **Script Conversion:** Seamlessly convert content between Cyrillic and Latin scripts.
- **Selective Transliteration:** Use shortcodes or tags to apply transliteration only to specific parts of your content.
- **Image Manipulation:** Display different images based on the selected script using simple shortcode.
- **Permalink Tool:** Convert Cyrillic permalinks to Latin with a single click.
- **Cyrillic User Profiles:** Create user profiles using Cyrillic script.
- **Permalink Translation:** Automatically translate all Cyrillic permalinks to Latin for better SEO and compatibility.
- **Media File Translation:** Convert filenames of uploaded media from Cyrillic to Latin.
- **Bilingual Search:** Enable WordPress search functionality to work with both Cyrillic and Latin scripts.
- **Developer Friendly:** Includes PHP functions for deeper integration into themes and plugins.

All settings and additional documentation are available within the plugin interface.

= FEATURES =

&#9989; Convert between Cyrillic and Latin scripts for posts, pages, terms, filenames, and permalinks
&#9989; Support for Cyrillic usernames
&#9989; Search content in both Cyrillic and Latin scripts
&#9989; WP-CLI support

= BENEFITS =

&#9989; Compatible with multilanguage, SEO plugins, and many WordPress template
&#9989; Supports partial transliteration and special characters
&#9989; Scalable, customizable, and lightweight with minimal page speed impact
&#9989; Multilingual support including diacritics for Serbian
&#9989; Compatible with [PHP 8.1](https://plugintests.com/plugins/wporg/serbian-transliteration/latest)

= LANGUAGE SUPPORT =

&#9989; **Serbian, Bosnian, Montenegrin, Russian, Belarusian, Bulgarian, Macedonian, Kazakh, Ukrainian, Georgian, Greek, Arabic, Armenian, Uzbek, Tajik, Kyrgyz, Mongolian, Bashkir**

&#128312; More languages coming soon...

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

This plugin can also serve as an alternative to [SrbTransLatin](https://wordpress.org/plugins/srbtranslatin/), [Cyr-To-Lat](https://wordpress.org/plugins/cyr2lat/), [Allow Cyrillic Usernames](https://wordpress.org/plugins/allow-cyrillic-usernames/), [Filenames to latin](https://wordpress.org/plugins/filenames-to-latin/), [Cyrillic Permalinks](https://wordpress.org/plugins/cyrillic-slugs/), [Latin Now!](https://wordpress.org/plugins/latin-now/), [Cyr to Lat enhanced](https://wordpress.org/plugins/cyr3lat/), [Cyrlitera](https://wordpress.org/plugins/cyrlitera/), [Geo to Lat](https://wordpress.org/plugins/geo-to-lat/) and other similar plugins. We have managed to combine all the necessary functionalities into one plugin, but if you want to have all the separate functions, we invite you to use some of these excellent plugins.

It is important for you to know that any functionality in our plugin can be turned off if you do not need it, as well as the ability to filter certain hooks and filters. We have tried to provide maximum flexibility and compatibility to everyone.

== Installation ==

1. **Install via WordPress Admin:**
   - Navigate to `WP-Admin -> Plugins -> Add New`.
   - In the search bar, type "WordPress Transliteration".
   - Once you find the plugin, click on the "Install Now" button.

2. **Install via Upload:**
   - Download the **serbian-transliteration.zip** file.
   - Go to `WP-Admin -> Plugins -> Add New -> Upload Plugin`.
   - Click on "Choose File", select the downloaded ZIP file, and then click "Install Now".
   - Alternatively, you can manually upload the unzipped plugin folder to the `/wp-content/plugins` directory via FTP.

3. **Activate the Plugin:**
   - Once the plugin is installed, go to `WP-Admin -> Plugins`.
   - Find "WordPress Transliteration" in the list and click "Activate".

4. **Configure the Plugin:**
   - After activation, go to `Settings -> Transliteration` in your WordPress admin panel.
   - Adjust the settings according to your needs and save the changes.

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

= 2.0.1 =
* Bug fix
* Adding stricter permalink transliteration
* Improved debugging

= 2.0.0 =
* Complete redesign and refactoring of the PHP code
* Full support for WordPress 6.6 and higher
* Compatibility with PHP 8.x versions
* Fixed issues with Cyrillic transliteration
* Enhanced optimization and better content control
* Added support for visual editors
* Bug fixes and improved system stability
* Ready for future extensions and new functionalities
* Added admin tools for easier content transliteration management
* Improved user interface with better accessibility options
* Streamlined settings page for more intuitive navigation
* Added support for multilingual content and automatic language detection

= 1.13.0 =
* Removed Alternate Links support
* Fixed problems with First Visit mode
* Fixed SEO problems with `_rstr_nocache` URL pharams
* Fixed SEO rewrite problems
* Preparing transition code for version 2.0.0

= 1.12.20 =
* Fixed caching support
* Fixed transliteration modes
* Fixed method errors
* Added "Light mode" as default

= 1.12.19 =
* Fix Fatal error on page builders

= 1.12.18 =
* Fix flush cache bug
* Fixed PHP errors
* Additional optimizations

= 1.12.17 =
* Fixing problems with cache plugins
* Fixing a memory leak bug
* Removing support for some PHP5.6 versions

= 1.12.16 =
* Fixed activation error

= 1.12.15 =
* Fixed issue with WooCommerce memory buffer
* Fixed activation issue

= 1.12.14 =
* Fix memory leak on WooComerce
* Improved cache support

= 1.12.13 =
* Fixed problems with editors that using the REST-API
* Fixed MySQL bugs
* Fixed translation bugs
* Optimized PHP code
* Getting ready for version 1.13.0

= 1.12.12 =
* Added WordPress REST API support
* Fixed WooCommerce REST API transliteration
* Fixed filter loading priorities

= 1.12.11 =
* Removed first visit auto mode, forced to latin
* Fixed PHP bugs in admin notices
* Improved translations

= 1.12.10 =
* Added support for the LSCache, Hyper Cache,Simple Cache, Autoptimize and WP-Optimize
* Improved "Light mode"

= 1.12.9 =
* Added support for PHP versions below 7.4.
* Fixed a PHP error related to the T_DOUBLE_ARROW syntax.

= 1.12.8 =
* Added new experimental "Light mode"
* Additional optimization of output buffers

= 1.12.7 =
* PHP bugfix in Standard mode

= 1.12.6 =
* Fixing WooCommerce minicart
* Fixing problem with percentages in textdomains
* Optimized PHP code

= 1.12.5 =
* Fixed script_selector() function
* Refactored code
* Improved plugin optimization for better performance

= 1.12.4 =
* Enhanced plugin optimization for better performance
* Code refactoring for improved readability and maintenance
* Corrected search functionality for more accurate results
* Advanced database caching for faster data access
* Upgraded Memcache implementation for more efficient cache management

= 1.12.3 =
* Plugin optimization
* Improved translations
* Fixed bugs in plugin detection

= 1.12.2 =
* Fixed transliteration of AJAX calls
* Fixed Woocommerce mini cart transliterations
* Fixed Template Switcher
* Improved translations

= 1.12.1 =
* Fixed problems with specifiers
* Optimized transliterations
* Added new languages
* Fixed HTML transliterations

= 1.12.0 =
* Fixed problems with Avada theme
* Fixed problems in WooCommerce plugin
* Fixed bugs in cyrillic encodings
* Added Georgian language
* Improvement of the Arabic language
* Improvement of the Armenian language

= 1.11.8 =
* Fixed problems with translations

= 1.11.7 =
* Fixed percentage problems
* Fixed Elementor bugs

= 1.11.6 =
* Fixed UX bugs
* Fixed translations

= 1.11.5 =
* Fixed warnings in PHP

= 1.11.4 =
* Added language exclusion
* Fixed bugs from previous version

= 1.11.3 =
* Fixed plugin and theme transliteration
* Fixed problem with duplicated percentages

= 1.11.2 =
* Fixed Avada theme support
* Fixed "print formatted" text support
* Fixed lat to cyr transliteration

= 1.11.1 =
* Fixed transliteration bug on certain themes
* Fixed WooCommerce integration

= 1.11.0 =
* ATTENTION: IMPORTANT UPDATE, PLEASE BE CAUTIOUS.
* Transliteration mode modified
* DOM component added
* HTML content check added
* Read and write buffer mode changed
* PHP code optimized
* Deprecated codes removed
* Transliteration added for emails through PHP mailer

== Upgrade Notice ==

= 2.0.1 =
* Bug fix
* Adding stricter permalink transliteration
* Improved debugging

= 2.0.0 =
* Complete redesign and refactoring of the PHP code
* Full support for WordPress 6.6 and higher
* Compatibility with PHP 8.x versions
* Fixed issues with Cyrillic transliteration
* Enhanced optimization and better content control
* Added support for visual editors
* Bug fixes and improved system stability
* Ready for future extensions and new functionalities
* Added admin tools for easier content transliteration management
* Improved user interface with better accessibility options
* Streamlined settings page for more intuitive navigation
* Added support for multilingual content and automatic language detection

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

You can customize the transliteration process by defining your own substitutions directly in your theme's `functions.php` file. This is done by using filters specific to the language you want to modify.

To create custom substitutions, use the following filter:

`add_filter( 'transliteration_map_{$locale}', 'function_callback', 10, 1 );`

Here's an example for Serbian (`sr_RS`) that demonstrates how to modify both single letters and multiple-letter combinations.

`/*
  * Modify conversion table for Serbian language.
  *
  * @param array $map Conversion map.
  *
  * @return array
  */
function my_transliteration__sr_RS( $map ) {

	// Example for 2 or more letters
	$new_map = [
		'Ња' => 'nja',
		'Ње' => 'nje',
		'Обједињени' => 'Objedinjeni'
	];
	$map = array_merge($new_map, $map);
	
	// Example for one letter
	$new_map = [
		'А' => 'X',
		'Б' => 'Y',
		'В' => 'Z'
	];
	$map = array_merge($map, $new_map);
	
	return $map;
}
add_filter( 'transliteration_map_sr_RS', 'my_transliteration__sr_RS', 10, 1 );`

In this example:
- The first `$new_map` array defines substitutions for combinations of two or more letters.
- The second `$new_map` array defines substitutions for individual letters.

This custom mapping will override the default transliteration rules for the specified language (`sr_RS` in this case).

== Other Notes ==

= Plugin Updates =
We regularly update the Transliterator plugin to improve its functionality, enhance performance, and ensure compatibility with the latest versions of WordPress and PHP. Keep your plugin up to date to benefit from the latest features and fixes. To stay informed about updates, visit our plugin page on WordPress.org or follow us on social media.

= Support and Feedback =
If you encounter any issues or have suggestions for improving the plugin, please don't hesitate to reach out. You can contact us through the support forum on WordPress.org, or directly via [GitHub repository](https://github.com/InfinitumForm/serbian-transliteration). We value your feedback and are committed to providing prompt and effective support.

= Compatibility =
The Transliterator plugin is compatible with a wide range of WordPress versions and works seamlessly with many popular plugins. However, due to the vast number of available plugins, there's a small chance of encountering conflicts. If you experience any issues, please check for plugin conflicts and update your WordPress installation and all plugins.

### Contributing =
We welcome contributions from the community! If you're a developer or a user with ideas for improvement, visit our [GitHub repository](https://github.com/InfinitumForm/serbian-transliteration) to contribute. You can report issues, suggest new features, or submit pull requests.

= Credits =
Special thanks to all contributors and beta testers who helped in developing and refining this plugin. Your feedback and support are invaluable.