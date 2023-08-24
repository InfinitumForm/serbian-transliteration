<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
$nonce = esc_attr(wp_create_nonce('rstr-transliteration-letters')); ?>
<div class="rstr-tab-wrapper" id="transliteration-letters">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/transliteration'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
            	<?php do_action('rstr/settings/tab/content/tools/action'); ?>
				<h1><span><?php _e('Converter for transliterating Cyrillic into Latin and vice versa', 'serbian-transliteration'); ?></span></h1>
                <?php printf('<p>%s</p>', __('Copy the desired text into one field and press the desired key to convert the text.', 'serbian-transliteration')); ?>
                
                <form class="rstr-row">
                	<div class="rstr-col">
                    	<textarea name="rstr-transliteration-letters" id="rstr-transliteration-letters" class="form-control" rows="10"></textarea>
                        <button type="button" class="button button-primary button-transliteration-letters" data-mode="cyr_to_lat" data-nonce="<?php echo esc_attr($nonce); ?>"><?php _e('Convert to Latin', 'serbian-transliteration'); ?></button>
                        <button type="button" class="button button-primary button-transliteration-letters" data-mode="lat_to_cyr" data-nonce="<?php echo esc_attr($nonce); ?>"><?php _e('Convert to Cyrillic', 'serbian-transliteration'); ?></button>
                    </div>
                    <div class="rstr-col">
                    	<textarea name="rstr-transliteration-letters-result" id="rstr-transliteration-letters-result" class="form-control" rows="10" readonly></textarea>
                        <button type="reset" class="button button-reset" data-mode="lat_to_cyr"><?php _e('Reset', 'serbian-transliteration'); ?></button>
                    </div>
                </form>
                
            </div>
        </div>
	</div>
</div>