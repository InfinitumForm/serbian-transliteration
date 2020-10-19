<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); } ?>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<form method="post" action="<?php echo esc_url(admin_url('/options.php')); ?>" id="<?php echo RSTR_NAME; ?>-settings-form">
				<?php
					settings_fields( RSTR_NAME . '-group' );
					settings_fields( RSTR_NAME . '-search' );
					do_settings_sections( RSTR_NAME );
					submit_button();
				?>
				</form>
			</div>
		</div>
		<br class="clear">
	</div>

</div>