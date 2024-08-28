<?php if ( !defined('WPINC') ) die();
$activations = get_option( RSTR_NAME . '-activation' );
$options = get_rstr_option();
?><br>
<table class="table table-sm table-striped w-100">
	<thead><?php do_action('rstr/settings/debug/table/thead'); ?></thead>
	<tbody>
		<?php do_action('rstr/settings/debug/table/tbody/start'); ?>
		<tr>
			<td width="30%" style="width:30%;"><strong><?php _e( 'Plugin ID', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo get_option(RSTR_NAME . '-ID'); ?></td>
		</tr>
		<tr>
			<td width="30%" style="width:30%;"><strong><?php _e( 'Plugin version', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo RSTR_VERSION; ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'WordPress version', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo get_bloginfo( 'version' ); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Last plugin update', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo (!empty($activations) ? end($activations) : '-'); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'PHP version', 'serbian-transliteration' ); ?></strong></td>
			<td>PHP <?php echo PHP_VERSION; ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'PHP version ID', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo PHP_VERSION_ID; ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'PHP architecture', 'serbian-transliteration' ); ?></strong></td>
			<td><?php printf(__('%d bit', 'serbian-transliteration'), (Transliteration_Debug::is_php64() ? 64 : 32)); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'WordPress debug', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo ( WP_DEBUG ? '<strong><span style="color:#007d1b">' . __( 'On', 'serbian-transliteration' ) . '</span></strong>' : __( 'Off', 'serbian-transliteration' ) ); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'WordPress multisite', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo ( RSTR_MULTISITE ? '<strong><span style="color:#007d1b">' . __( 'On', 'serbian-transliteration' ) . '</span></strong>' : __( 'Off', 'serbian-transliteration' ) ); ?></td>
		</tr>
	<?php if(RSTR_WOOCOMMERCE) : ?>
		<tr>
			<td><strong><?php _e( 'WooCommerce active', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo ( RSTR_WOOCOMMERCE ? '<strong><span style="color:#007d1b">' . __( 'On', 'serbian-transliteration' ) . '</span></strong>' : __( 'Off', 'serbian-transliteration' ) ); ?></td>
		</tr>
		<?php if(defined('WC_VERSION')) : ?>
		<tr>
			<td><strong><?php _e( 'WooCommerce version', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo WC_VERSION; ?></td>
		</tr>
		<?php endif; ?>
	<?php endif; ?>
		<tr>
			<td><strong><?php _e( 'Site title', 'serbian-transliteration' ); ?></strong></td>
			<td><?php if($name = get_bloginfo( 'name' )) {
				echo $name;
			} else {
				echo '-';
			} ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Tagline', 'serbian-transliteration' ); ?></strong></td>
			<td><?php if($description = get_bloginfo( 'description' )) {
				echo $description;
			} else {
				echo '-';
			} ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'WordPress address (URL)', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo get_bloginfo( 'wpurl' ); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Admin email', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo get_bloginfo( 'admin_email' ); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Encoding for pages and feeds', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo get_bloginfo( 'charset' ); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Content-Type', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo get_bloginfo( 'html_type' ); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Site Language', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo get_locale(); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Server time', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo date( 'r' ); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'WordPress directory path', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo ABSPATH; ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Operting system', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo Transliteration_Debug::getOS(); ?> <?php printf(__('%d bit', 'serbian-transliteration'), Transliteration_Debug::architecture()); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'User agent', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo Transliteration_Debug::user_agent(); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e( 'Plugin directory path', 'serbian-transliteration' ); ?></strong></td>
			<td><?php echo RSTR_ROOT; ?></td>
		</tr>
		<?php do_action('rstr/settings/debug/table/tbody/end'); ?>
	</tbody>
	<tfoot><?php do_action('rstr/settings/debug/table/tfoot'); ?></tfoot>
</table>
<br><br>
<div class="accordion-container">
	<button class="accordion-link" type="button"><?php _e('Plugin settings', 'serbian-transliteration'); ?></button>
	<div class="accordion-panel" style="padding:0;">
		<pre class="lang-php" style="margin: 0;">
["<?php echo RSTR_NAME; ?>"] => <?php print_r($options); ?>
		</pre>
	</div>
</div>