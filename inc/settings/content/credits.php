<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); };

$special_thanks_sponsors = array(
	'INFINITUM FORM' => 'https://infinitumform.com/',
	'UG PREOBRAŽENJE' => 'https://preobrazenje.rs/',
	'CNZD' => 'https://cnzd.rs/'
);

$special_thanks = array(
	'dizajn24' => 'https://profiles.wordpress.org/dizajn24',
	'tihi' => 'https://profiles.wordpress.org/tihi',
	'Slobodan Pantović' => '',
	'Ivan Stanojević' => ''
);

?>
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
					'<p>%s</p>', 
					sprintf(__(
						'This plugin is made by %1$s', RSTR_NAME),
						'<a href="https://www.linkedin.com/in/ivijanstefanstipic/" target="_blank"><em><strong>Ivijan-Stefan Stipić</strong></em></a>'
					)
				); ?>
                <?php printf('<p>%s</p>', __('This is a light weight, simple and easy plugin with which you can transliterate your WordPress installation from Cyrillic to Latin and vice versa in a few clicks. This transliteration plugin also supports special shortcodes that you can use to partially transliterate parts of the content.', RSTR_NAME)); ?>
                <p><?php
				
					$special_thanks_sponsors_render = array();
        
		        	foreach($special_thanks_sponsors as $name => $thanks_url){
						if(!empty($thanks_url)) {
							$special_thanks_sponsors_render[sanitize_title($name)]= '<a href="' . esc_url($thanks_url) . '" target="_blank">' . $name . '</a>';
						} else {
							$special_thanks_sponsors_render[sanitize_title($name)]= $name;
						}
					}
					
					printf(
						'<strong>%s</strong> %s %s',
						__('Sponsors of this plugin:', RSTR_NAME),
						join(', ', $special_thanks_sponsors_render),
						sprintf(
							'( %s )',
							sprintf(__('If you want to help develop this plugin and be one of the sponsors, please contact us at: %s', RSTR_NAME), '<a href="mailto:infinitumform@gmail.com">infinitumform@gmail.com</a>')
						)
					);
					
				?></p>
			<?php if(!empty($special_thanks) && $plugin_info = Serbian_Transliteration_Utilities::plugin_info(array('contributors' => true, 'donate_link' => false))) : ?>
				<p><?php
				
                	$special_thanks_render = array();
					
					foreach($plugin_info->contributors as $username => $info) {
						if(in_array($username, array('ivijanstefan', 'creativform', 'infinitumform'))) continue;
						$info = (object)$info;
						$special_thanks_render[sanitize_title($info->display_name)]= '<a href="' . esc_url($info->profile) . '" target="_blank">' . $info->display_name . '</a>';
					}
					
					foreach($special_thanks as $name => $thanks_url){
						if(!empty($thanks_url)) {
							$special_thanks_render[sanitize_title($name)]= '<a href="' . esc_url($thanks_url) . '" target="_blank">' . $name . '</a>';
						} else {
							$special_thanks_render[sanitize_title($name)]= $name;
						}
					}
					
					printf('<strong>%s</strong> %s', __('Special thanks to the contributors in the development of this plugin:', RSTR_NAME), join(', ', $special_thanks_render));
					
				?></p>
                
            <?php endif; ?>
                
                <h3>&copy; <?php _e('Copyright', RSTR_NAME); ?></h3>
                <?php printf(
					'<p>%s</p>',
					sprintf( 
						__('Copyright &copy; 2020 - %1$d %2$s by %3$s. All Right Reserved.', RSTR_NAME),
						date("Y"),
						'<a href="https://wordpress.org/plugins/serbian-transliteration/" target="_blank"><em><strong>' . __('Transliterator – WordPress Transliteration', RSTR_NAME) . '</strong></em></a>',
						'<a href="https://www.linkedin.com/in/ivijanstefanstipic/" target="_blank"><em><strong>Ivijan-Stefan Stipić</strong></em></a>'
					)
				); ?>
                
                <?php printf('<p>%s</p>', __('This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.', RSTR_NAME)); ?>
                <?php printf('<p>%s</p>', __('This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.', RSTR_NAME)); ?>
                <p><a href="javascript:void(0);" onClick="rstr_popup('<?php echo RSTR_URL; ?>/LICENSE.txt','GNU GENERAL PUBLIC LICENSE','550','450');"><?php _e('See the GNU General Public License for more details.', RSTR_NAME); ?></a></p>
                <?php printf('<p>%s</p>', __('You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.', RSTR_NAME)); ?>
				
				<h3><span class="dashicons dashicons-heart"></span> <?php _e('Donations', RSTR_NAME); ?></h3>
				<?php printf('<p>%s</p>', __('This plugin is 100% free. If you want to buy us one coffee, beer or in general help the development of this plugin through a monetary donation, you can do it in the following ways:', RSTR_NAME)); ?>
				<ul>
					<?php printf('<li><b>%s</b>: %s</li>', __('PayPal', RSTR_NAME), 'creativform@gmail.com'); ?>
					<?php printf('<li><b>%s</b>: %s (%s)</li>', __('From Serbia', RSTR_NAME), '115-0000000138835-77', __('Mobi Bank', RSTR_NAME)); ?>
				</ul>
			</div>
		</div>
		<br class="clear">
	</div>

</div>