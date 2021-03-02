<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
	include_once RSTR_INC . '/OS.php';
	$activations = get_option( RSTR_NAME . '-activation' );
	$options = get_rstr_option();
?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('.lang-php').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/debug'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<h2><span><?php _e('Debug information', RSTR_NAME); ?></span></h2>
				<table class="table table-sm table-striped w-100">
					<thead><?php do_action('rstr/settings/debug/table/thead'); ?></thead>
					<tbody>
						<?php do_action('rstr/settings/debug/table/tbody/start'); ?>
						<tr>
							<td width="30%" style="width:30%;"><strong><?php _e( 'Plugin ID', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_option(RSTR_NAME . '-ID'); ?></td>
						</tr>
						<tr>
							<td width="30%" style="width:30%;"><strong><?php _e( 'Plugin version', RSTR_NAME ); ?></strong></td>
							<td><?php echo RSTR_VERSION; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress version', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'version' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Last plugin update', RSTR_NAME ); ?></strong></td>
							<td><?php echo (!empty($activations) ? end($activations) : '-'); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'PHP version', RSTR_NAME ); ?></strong></td>
							<td>PHP <?php echo PHP_VERSION; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'PHP version ID', RSTR_NAME ); ?></strong></td>
							<td><?php echo PHP_VERSION_ID; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'PHP architecture', RSTR_NAME ); ?></strong></td>
							<td><?php printf(__('%d bit', RSTR_NAME), (Serbian_Transliteration_OS::is_php64() ? 64 : 32)); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress debug', RSTR_NAME ); ?></strong></td>
							<td><?php echo ( WP_DEBUG ? '<strong><span style="color:#007d1b">' . __( 'On', RSTR_NAME ) . '</span></strong>' : __( 'Off', RSTR_NAME ) ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress multisite', RSTR_NAME ); ?></strong></td>
							<td><?php echo ( RSTR_MULTISITE ? '<strong><span style="color:#007d1b">' . __( 'On', RSTR_NAME ) . '</span></strong>' : __( 'Off', RSTR_NAME ) ); ?></td>
						</tr>
					<?php if(RSTR_WOOCOMMERCE) : ?>
						<tr>
							<td><strong><?php _e( 'WooCommerce active', RSTR_NAME ); ?></strong></td>
							<td><?php echo ( RSTR_WOOCOMMERCE ? '<strong><span style="color:#007d1b">' . __( 'On', RSTR_NAME ) . '</span></strong>' : __( 'Off', RSTR_NAME ) ); ?></td>
						</tr>
						<?php if(defined('WC_VERSION')) : ?>
						<tr>
							<td><strong><?php _e( 'WooCommerce version', RSTR_NAME ); ?></strong></td>
							<td><?php echo WC_VERSION; ?></td>
						</tr>
						<?php endif; ?>
					<?php endif; ?>
						<tr>
							<td><strong><?php _e( 'Site title', RSTR_NAME ); ?></strong></td>
							<td><?php if($name = get_bloginfo( 'name' )) {
								echo $name;
							} else {
								echo '-';
							} ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Tagline', RSTR_NAME ); ?></strong></td>
							<td><?php if($description = get_bloginfo( 'description' )) {
								echo $description;
							} else {
								echo '-';
							} ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress address (URL)', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'wpurl' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Admin email', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'admin_email' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Encoding for pages and feeds', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'charset' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Content-Type', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'html_type' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Site Language', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_locale(); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Server time', RSTR_NAME ); ?></strong></td>
							<td><?php echo date( 'r' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress directory path', RSTR_NAME ); ?></strong></td>
							<td><?php echo ABSPATH; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Operting system', RSTR_NAME ); ?></strong></td>
							<td><?php echo Serbian_Transliteration_OS::getOS(); ?> <?php printf(__('%d bit', RSTR_NAME), Serbian_Transliteration_OS::architecture()); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'User agent', RSTR_NAME ); ?></strong></td>
							<td><?php echo Serbian_Transliteration_OS::user_agent(); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Plugin directory path', RSTR_NAME ); ?></strong></td>
							<td><?php echo RSTR_ROOT; ?></td>
						</tr>
						<?php do_action('rstr/settings/debug/table/tbody/end'); ?>
					</tbody>
					<tfoot><?php do_action('rstr/settings/debug/table/tfoot'); ?></tfoot>
				</table>
				<br>			
				<div class="accordion-container">
					<button class="accordion-link" type="button"><?php _e('Plugin settings', RSTR_NAME); ?></button>
					<div class="accordion-panel" style="padding:0;">
						<pre class="lang-php" style="margin: 0;">
["<?php echo RSTR_NAME; ?>"] => <?php var_dump($options); ?>
						</pre>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>

</div>