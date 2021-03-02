<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); } ?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('code.lang-php').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="rstr-tab-wrapper" id="documentation-page">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/functions'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
            	<?php do_action('rstr/settings/tab/content/tools/documentation'); ?>
				<h1><span><?php _e('Available PHP Functions', RSTR_NAME); ?></span></h1>
				<div class="inside">
					<br>
					<h2 style="margin:0;">is_cyrillic_text</h2>
					<p><code class="lang-php">function is_cyrillic_text(string $content) : bool</code></p>
					<br>
					<h2 style="margin:0;">is_latin_text</h2>
					<p><code class="lang-php">function is_latin_text(string $content) : bool</code></p>
					<br>
					<h2 style="margin:0;">is_already_cyrillic</h2>
					<?php printf('<p>%s</p>', __('Determines whether the site is already in Cyrillic.', RSTR_NAME)); ?>
					<p><code class="lang-php">function is_already_cyrillic() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_cyrillic</h2>
					<p><code class="lang-php">function is_cyrillic() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_latin</h2>
					<p><code class="lang-php">function is_latin() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_serbian</h2>
					<p><code class="lang-php">function is_serbian() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_russian</h2>
					<p><code class="lang-php">function is_russian() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_belarusian</h2>
					<p><code class="lang-php">function is_belarusian() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_bulgarian</h2>
					<p><code class="lang-php">function is_bulgarian() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_macedonian</h2>
					<p><code class="lang-php">function is_macedonian() : bool</code></p>
					<br>
					<h2 style="margin:0;">is_kazakh</h2>
					<p><code class="lang-php">function is_kazakh() : bool</code></p>
					<br>
					<h2 style="margin:0;">transliterate</h2>
					<?php printf('<p>%s</p>', __('Transliteration of some text or content into the desired script.', RSTR_NAME)); ?>
					<p><code class="lang-php">function transliterate(string $content, string $type='cyr_to_lat', bool $fix_html = true) : string</code></p>
					<?php printf('<p>%s</p>', __('The <b><i>$type</i></b> parameter has two values: <code>cyr_to_lat</code> (Cyrillic to Latin) and <code>lat_to_cyr</code> (Latin to Cyrillic)', RSTR_NAME)); ?>
                    <br>
                    <h2 style="margin:0;">cyr_to_lat</h2>
					<?php printf('<p>%s</p>', __('Transliteration only from Cyrillic to Latin.', RSTR_NAME)); ?>
					<p><code class="lang-php">function cyr_to_lat(string $content) : string</code></p>
					<br>
                    <h2 style="margin:0;">lat_to_cyr</h2>
                    <?php printf('<p>%s</p>', __('Transliteration only from Latin to Cyrillic.', RSTR_NAME)); ?>
					<p><code class="lang-php">function lat_to_cyr(string $content, bool $fix_html = true, bool $fix_diacritics = false) : string</code></p>
					<br>
					<h2 style="margin:0;">get_script</h2>
					<?php printf('<p>%s</p>', __('Get active script.', RSTR_NAME)); ?>
					<p><code class="lang-php">function get_script() : string</code></p>
					<br>
					<h2 style="margin:0;">script_selector</h2>
					<?php printf('<p>%s</p>', __('This function displays a selector for the transliteration script.', RSTR_NAME)); ?>
					<p><code class="lang-php">function script_selector(array $args) : string|echo|array|object</code></p>
					<h3><?php _e('Parameters', RSTR_NAME); ?></h3>
					<?php printf('<p><b><code>$args</code></b> (array) - %1$s</p>', __('This attribute contains an associative set of parameters for this function:', RSTR_NAME)); ?>
					<ul>
						<?php printf(
							'<li><code>%1$s</code> - %2$s</li>',
							'display_type',
							sprintf(
								__('(string) The type of selector that will be displayed on the site. It can be: %1$s, %2$s, %3$s, %4$s, %5$s or %6$s. Default: %1$s', RSTR_NAME),
								'<code>inline</code>',
								'<code>select</code>',
								'<code>list</code>',
								'<code>list_items</code>',
								'<code>array</code>',
								'<code>object</code>'
							)
						); ?>
						<?php printf(
							'<li><code>%1$s</code> - %2$s</li>',
							'echo',
							sprintf(__('(bool) determines whether it will be displayed through an echo or as a string. Default: %s', RSTR_NAME), '<code>false</code>')
						); ?>
						<?php printf(
							'<li><code>%1$s</code> - %2$s</li>',
							'separator',
							sprintf(__('(string) Separator to be used when the selector type is %s. Default: %s', RSTR_NAME), '<code>inline</code>', '<code> | </code>')
						); ?>
						<?php printf(
							'<li><code>%1$s</code> - %2$s</li>',
							'cyr_caption',
							sprintf(__('(string) Text for Cyrillic link. Default: %s', RSTR_NAME), '<code>' . __('Cyrillic', RSTR_NAME) . '</code>')
						); ?>
						<?php printf(
							'<li><code>%1$s</code> - %2$s</li>',
							'lat_caption',
							sprintf(__('(string) Text for Latin link. Default: %s', RSTR_NAME), '<code>' . __('Latin', RSTR_NAME) . '</code>')
						); ?>
					</ul>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>

</div>