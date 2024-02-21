<?php if ( !defined('WPINC') ) die(); ?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('.lang-txt').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="rstr-tab-wrapper" id="documentation-page">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/tags'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
            	<?php do_action('rstr/settings/tab/content/tools/documentation'); ?>
				<h1><span><?php _e('Available Tags', 'serbian-transliteration'); ?></span></h1>
                <p class="description"><?php _e('These tags have a special purpose and work separately from short codes and can be used in fields where short codes cannot be used.', 'serbian-transliteration'); ?><br><?php _e('These tags have no additional settings and can be applied in plugins, themes, widgets and within other short codes.', 'serbian-transliteration'); ?></p>
				<div class="inside">
					<br>
                    <h2 style="margin:0;"><?php _e('Cyrillic to Latin', 'serbian-transliteration'); ?>:</h2>
					<p><code class="lang-txt">{<span class="hljs-title">cyr_to_lat</span>}Ћирилица у латиницу{/<span class="hljs-title">cyr_to_lat</span>}</code></p>
					<br>
					<h2 style="margin:0;"><?php _e('Latin to Cyrillic', 'serbian-transliteration'); ?>:</h2>
					<p><code class="lang-txt">{<span class="hljs-title">lat_to_cyr</span>}Latinica u ćirilicu{/<span class="hljs-title">lat_to_cyr</span>}</code></p>
                    <br>
					<h2 style="margin:0;"><?php _e('Skip transliteration', 'serbian-transliteration'); ?>:</h2>
					<p><code class="lang-txt">{<span class="hljs-title">rstr_skip</span>}<?php _e('Keep this original', 'serbian-transliteration'); ?>{/<span class="hljs-title">rstr_skip</span>}</code></p>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>

</div>