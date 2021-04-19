<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); } ?>
<script>function rstr_popup(url, title, w, h) {
	// Fixes dual-screen position Most browsers Firefox
	var dualScreenLeft = (window.screenLeft != undefined ? window.screenLeft : screen.left),
		dualScreenTop = (window.screenTop != undefined ? window.screenTop : screen.top);
	
	width = (window.innerWidth ? window.innerWidth : (document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width));
	height = (window.innerHeight ? window.innerHeight : (document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height));
	
	var left = ((width / 2) - (w / 2)) + dualScreenLeft,
		top = ((height / 2) - (h / 2)) + dualScreenTop,
		newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
	
	// Puts focus on the newWindow
	if (window.focus) {
		newWindow.focus();
	}
};</script>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/credits'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">         
                <h3><span class="dashicons dashicons-info"></span> <?php _e('Credits', RSTR_NAME); ?></h3>
                <?php printf(
					'<p>%s<p>', 
					sprintf(__(
						'This plugin is made by %1$s', RSTR_NAME),
						'<a href="https://www.linkedin.com/in/ivijanstefanstipic/" target="_blank"><em><strong>Ivijan-Stefan Stipić</strong></em></a>'
					)
				); ?>
                <?php printf('<p>%s<p>', __('This is a light weight, simple and easy plugin with which you can transliterate your WordPress installation from Cyrillic to Latin and vice versa in a few clicks. This transliteration plugin also supports special shortcodes that you can use to partially transliterate parts of the content.', RSTR_NAME)); ?>
                
			<?php if($plugin_info = Serbian_Transliteration_Utilities::plugin_info(array('contributors' => true, 'donate_link' => false))) : ?>
				<p><?php printf('<strong>%s</strong>', __('Special thanks to the contributors in the development of this plugin:', RSTR_NAME)); ?> <?php
                	foreach($plugin_info->contributors as $username => $info) {
						if($username == 'ivijanstefan') continue;
						$info = (object)$info;
						echo '<a href="' . esc_url($info->profile) . '" target="_blank">' . $info->display_name . '</a>, ';
					}
				?>Ivan Stanojević, Slobodan Pantović</p>
            <?php endif; ?>
                
                <h3>&copy; <?php _e('Copyright', RSTR_NAME); ?></h3>
                <?php printf(
					'<p>%s<p>',
					sprintf( 
						__('Copyright &copy; 2020 - %1$d %2$s by %3$s. All Right Reserved.', RSTR_NAME),
						date("Y"),
						'<a href="https://wordpress.org/plugins/serbian-transliteration/" target="_blank"><em><strong>' . __('Transliterator – WordPress Transliteration', RSTR_NAME) . '</strong></em></a>',
						'<a href="https://www.linkedin.com/in/ivijanstefanstipic/" target="_blank"><em><strong>Ivijan-Stefan Stipić</strong></em></a>'
					)
				); ?>
                
                <?php printf('<p>%s<p>', __('This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.', RSTR_NAME)); ?>
                <?php printf('<p>%s<p>', __('This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.', RSTR_NAME)); ?>
                <p><a href="javascript:void(0);" onClick="rstr_popup('<?php echo RSTR_URL; ?>/LICENSE.txt','GNU GENERAL PUBLIC LICENSE','550','450');"><?php _e('See the GNU General Public License for more details.', RSTR_NAME); ?></a></p>
                <?php printf('<p>%s<p>', __('You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.', RSTR_NAME)); ?>
			</div>
		</div>
		<br class="clear">
	</div>

</div>