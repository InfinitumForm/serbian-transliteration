<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); } ?>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/shortcodes'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
	            <?php do_action('rstr/settings/tab/content/tools/action'); ?>
				<h1><span><?php _e('Permalink Transliteration Tool', RSTR_NAME); ?></span></h1>
				<?php
					printf('<p>%s</p>', __('This tool can rename all existing Cyrillic permalinks to Latin inside database.', RSTR_NAME));
					printf('<p>%s</p>', __('This option is dangerous and can create unexpected problems. Once you run this script, all permalinks in your database will be modified and this can affect on the SEO causing a 404 error.',
					RSTR_NAME));
					printf('<p>%s</p>', __('Consult your SEO developer before you run this script as you will then need to resubmit the sitemap and make any other additional settings to change the permalinks on the search engines.', RSTR_NAME));
					
					printf('<p>%s: <code>wp transliterate permalinks</code></p>', __('If you are using WP-CLI, this function can also be started with a simple shell command', RSTR_NAME));
					
					printf('<p><strong class="text-danger">%s</strong></p>', sprintf(__('You must %s before running this script.', RSTR_NAME), '<a href="https://wordpress.org/support/article/wordpress-backups/" target="_blank">' . __('back up the database', RSTR_NAME) . '</a>'));

					$get_post_types = get_post_types(array(
						'public'   => true
					), 'names', 'and');
					/*
					$post_types = array_map(function($match){
						return '<code><b>' . $match . '</b></code>';
					}, $get_post_types);
					*/
					$post_types_selector = array_map(function($match){
						return sprintf('<label for="tools-transliterate-post-type-%1$s"><input type="checkbox" id="tools-transliterate-post-type-%1$s" value="%1$s" class="tools-transliterate-permalinks-post-types" name="tools-transliterate-permalinks-post-types[]" checked><span>%2$s</span></label>', $match, strtr($match, array('_'=>' ',  '-'=>' ')));
					}, $get_post_types);
					
					printf('<p>%s</p>', sprintf(__('This tool will affect on the following post types: %s', RSTR_NAME), '<br>' . join('&nbsp;&nbsp;&nbsp;&nbsp; ', $post_types_selector)));
				?>
				<br>
				<div id="rstr-progress-bar" style="display:none;">
					<p class="progress-value" style="width:33%" data-value="33"></p>
					<progress max="100" value="33" class="php">
						<!-- Browsers that support HTML5 progress element will ignore the html inside `progress` element. Whereas older browsers will ignore the `progress` element and instead render the html inside it. -->
						<div class="progress-bar">
							<span style="width: 33%">33%</span>
						</div>
					</progress>
					<p class="progress-message"><?php _e('Please wait! Do not close the window or leave the page until this operation is completed!', RSTR_NAME); ?></p>
				</div>
				<p>
					<input type="button" id="<?php echo RSTR_NAME ?>-tools-transliterate-permalinks" class="button button-primary" data-nonce="<?php echo esc_attr(wp_create_nonce('rstr-run-permalink-transliteration')); ?>" value="<?php esc_attr_e('Let\'s do magic', RSTR_NAME); ?>" disabled>
					&nbsp;&nbsp;&nbsp;
					<label for="<?php echo RSTR_NAME ?>-tools-check">
						<input type="checkbox" id="<?php echo RSTR_NAME ?>-tools-check" value="1"> <?php _e('Are you sure you want this?', RSTR_NAME); ?>
					</label>
				</p>
				<blockquote id="rstr-disclaimer" style="display:none;">
					<h3><?php _e('Disclaimer', RSTR_NAME); ?></h3>
					<?php printf('<p>%s</p>', __('This tool is made to work safely but there is always a small chance of some unpredictable problem.', RSTR_NAME)); ?>
					<?php printf('<p><b>%s</b></p>', __('WE DO NOT GUARANTEE THAT THIS TOOL WILL FUNCTION PROPERLY ON YOUR SERVER AND BY USING THIS TOOL YOU ARE RESPONSIBLE FOR RISK AND POSSIBLE PROBLEMS.', RSTR_NAME)); ?>
					<?php printf('<p><b>%s</b></p>', __('BACKUP YOUR DATABASE BEFORE USE IT.', RSTR_NAME)); ?>
				</blockquote>
			</div>
		</div>
		<br class="clear">
	</div>

</div>