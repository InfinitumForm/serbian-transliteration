<?php if ( !defined('WPINC') ) die();

class Transliteration_Settings_Sidebars {
	
	public function donations() {
		?>
		<?php printf('<p>%s</p>', __('When you find a tool that fits your needs perfectly, and it\'s free, it’s something special. That’s exactly what my plugin is – free, but crafted with love and dedication. To ensure the continued development and improvement of the Transliterator plugin, I have established a foundation to support its future growth.', 'serbian-transliteration')); ?>
		<?php printf('<p>%s</p>', __('Your support for this project is not just an investment in the tool you use, but also a contribution to the community that relies on it. If you’d like to support the development and enable new features, you can do so through donations:', 'serbian-transliteration'));?>
		<p><a href="https://www.buymeacoffee.com/ivijanstefan" target="_blank"><img src="https://img.buymeacoffee.com/button-api/?text=<?php esc_attr_e('Buy me a coffee', 'serbian-transliteration'); ?>&emoji=&slug=ivijanstefan&button_colour=FFDD00&font_colour=000000&font_family=Bree&outline_colour=000000&coffee_colour=ffffff" /></a></p>
		<ul>
			<?php printf('<li><b>%s</b>: %s</li>', __('Mobi Bank', 'serbian-transliteration'), '115-0000000138835-77'); ?>
		</ul>
		<?php printf('<p>%s</p>', __('Every donation, no matter the amount, directly supports the ongoing work on the plugin and allows me to continue innovating. Thank you for supporting this project and being part of a community that believes in its importance.', 'serbian-transliteration')); ?>
		<?php printf('<p>%s<br><a href="https://www.linkedin.com/in/ivijanstefanstipic/" target="_blank">Ivijan-Stefan Stipić</a></p>', __('With love,', 'serbian-transliteration'));
	}
	
	public function contributors() {
		if($plugin_info = Transliteration_Utilities::plugin_info(array('contributors' => true, 'donate_link' => true))) : ?>
		<div class="rstr-inside-metabox flex">
			<?php foreach($plugin_info->contributors as $username => $info) : $info = (object)$info; ?>
			<div class="contributor contributor-<?php echo esc_attr($username); ?>" id="contributor-<?php echo esc_attr($username); ?>">
				<a href="<?php echo esc_url($info->profile); ?>" target="_blank">
					<img src="<?php echo esc_url($info->avatar); ?>">
					<h3><?php echo esc_html($info->display_name); ?></h3>
				</a>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="rstr-inside-metabox">
			<?php printf('<p>%s</p>', sprintf(__('If you want to support our work and effort, if you have new ideas or want to improve the existing code, %s.', 'serbian-transliteration'), '<a href="https://github.com/CreativForm/serbian-transliteration" target="_blank">' . __('join our team', 'serbian-transliteration') . '</a>')); ?>
			<?php /* printf('<p>%s</p>', sprintf(__('If you want to help further plugin development, you can also %s.', 'serbian-transliteration'), '<a href="' . esc_url($plugin_info->donate_link) . '" target="_blank">' . __('donate something for effort', 'serbian-transliteration') . '</a>')); */ ?>
		</div>
		<?php endif;
	}
	
}