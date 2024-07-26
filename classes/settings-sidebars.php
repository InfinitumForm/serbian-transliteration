<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Settings_Sidebars', false) ) : class Transliteration_Settings_Sidebars {
	
	public function donations() {
		?>
	<!-- img src="<?php echo esc_url(RSTR_ASSETS.'/img/cheers.jpg'); ?>" style="margin: 15px auto;" / -->
	<?php printf('<p>%s</p>', __('You know that feeling when you find something perfectly free? Well, that\'s my plugin - absolutely free, but created with lots of love and effort. To keep it all great (and to allow me to occasionally enjoy a coffee or beer while coding), your support means the world to me!', 'serbian-transliteration')); ?>
	<?php printf('<p>%s</p>', __('If you like my work and want to contribute to my coffee/beer/future development fund, here\'s how you can do it:', 'serbian-transliteration'));?>
	<p><a href="https://www.buymeacoffee.com/ivijanstefan" target="_blank"><img src="https://img.buymeacoffee.com/button-api/?text=<?php esc_attr_e('Buy me a coffee', 'serbian-transliteration'); ?>&emoji=&slug=ivijanstefan&button_colour=FFDD00&font_colour=000000&font_family=Cookie&outline_colour=000000&coffee_colour=ffffff" /></a></p>
	<ul>
		<?php printf('<li><b>%s</b>: %s</li>', __('Mobi Bank', 'serbian-transliteration'), '115-0000000138835-77'); ?>
	</ul>
	<?php printf('<p>%s</p>', __('Every donation, no matter the size, helps me continue my work and makes me happier than caffeine ever could. Thank you for being a part of my WordPress family!', 'serbian-transliteration')); ?>
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
	
} endif;