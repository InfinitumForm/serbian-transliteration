<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }

if(!class_exists('Serbian_Transliteration_Settings')) :
class Serbian_Transliteration_Settings_Sidebar extends Serbian_Transliteration
{
	private static $_instance = null;
	private $obj;
	
	function __construct($object)
	{
		$this->obj = $object;
		
		$this->add_action('rstr/settings/sidebar', 'postbox_contributors');
		$this->add_action('rstr/settings/sidebar', 'postbox_donations');
	//	$this->add_action('rstr/settings/sidebar', 'postbox_cloud_hosting');
	//	$this->add_action('rstr/settings/sidebar', 'postbox_infinitum');
		
		$this->add_action('rstr/settings/sidebar/tab/shortcodes', 'postbox_contributors');
		$this->add_action('rstr/settings/sidebar/tab/shortcodes', 'postbox_donations');
	//	$this->add_action('rstr/settings/sidebar/tab/shortcodes', 'postbox_infinitum');
		
		$this->add_action('rstr/settings/sidebar/tab/functions', 'postbox_contributors');
		$this->add_action('rstr/settings/sidebar/tab/functions', 'postbox_donations');
	//	$this->add_action('rstr/settings/sidebar/tab/functions', 'postbox_infinitum');
		
	//	$this->add_action('rstr/settings/sidebar/tab/permalink_tool', 'postbox_contributors');
		
		$this->add_action('rstr/settings/sidebar/tab/debug', 'postbox_contributors');
		$this->add_action('rstr/settings/sidebar/tab/debug', 'postbox_donations');
		
		$this->add_action('rstr/settings/sidebar/tab/credits', 'postbox_contributors');
		$this->add_action('rstr/settings/sidebar/tab/credits', 'postbox_donations_simple');
		$this->add_action('rstr/settings/sidebar/tab/credits', 'postbox_buymeacoffee');
		
		$this->add_action('rstr/settings/sidebar/tab/tags', 'postbox_contributors');
		$this->add_action('rstr/settings/sidebar/tab/tags', 'postbox_donations');
		
		$this->add_action('rstr/settings/sidebar/tab/transliteration', 'postbox_contributors');
		$this->add_action('rstr/settings/sidebar/tab/transliteration', 'postbox_donations');
		
	//	$this->add_action('rstr/settings/sidebar/tab/tags', 'postbox_infinitum');
		
	//	$this->add_action('rstr/settings/sidebar/tab/transliteration', 'postbox_cloud_hosting');
	//	$this->add_action('rstr/settings/sidebar/tab/transliteration', 'postbox_infinitum');
	}
	
	public static function instance($object)
	{
		$class = get_called_class();
		if(!$class){
			$class = self::class;
		}
		$instance = Serbian_Transliteration_Cache::get($class);
		if ( !$instance ) {
			$instance = Serbian_Transliteration_Cache::set($class, new self($object));
		}
		return $instance;
	}
	
	public function postbox_donations(){ ?>
<div class="postbox" id="donations">
	<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;">🌟 <span><?php _e('Brighten my day as my plugin brightens yours!', 'serbian-transliteration'); ?></span> 🌟</h3><hr>
	<div class="inside">
	<!-- img src="<?php echo esc_url(RSTR_ASSETS.'/img/cheers.jpg'); ?>" style="margin: 15px auto;" / -->
	<?php printf('<p>%s</p>', __('You know that feeling when you find something perfectly free? Well, that\'s my plugin - absolutely free, but created with lots of love and effort. To keep it all great (and to allow me to occasionally enjoy a coffee or beer while coding), your support means the world to me!', 'serbian-transliteration')); ?>
	<?php printf('<p>%s</p>', __('If you like my work and want to contribute to my coffee/beer/future development fund, here\'s how you can do it:', 'serbian-transliteration'));?>
	<p><a href="https://www.buymeacoffee.com/ivijanstefan" target="_blank"><img src="https://img.buymeacoffee.com/button-api/?text=<?php esc_attr_e('Buy me a coffee', 'serbian-transliteration'); ?>&emoji=&slug=ivijanstefan&button_colour=FFDD00&font_colour=000000&font_family=Cookie&outline_colour=000000&coffee_colour=ffffff" /></a></p>
	<ul>
		<?php printf('<li><b>%s</b>: %s</li>', __('Mobi Bank', 'serbian-transliteration'), '115-0000000138835-77'); ?>
	</ul>
	<?php printf('<p>%s</p>', __('Every donation, no matter the size, helps me continue my work and makes me happier than caffeine ever could. Thank you for being a part of my WordPress family!', 'serbian-transliteration')); ?>
	<?php printf('<p>%s<br><a href="https://www.linkedin.com/in/ivijanstefanstipic/" target="_blank">Ivijan-Stefan Stipić</a></p>', __('With love,', 'serbian-transliteration')); ?>
	
	</div>
</div>
	<?php }
	
	public function postbox_donations_simple(){ ?>
<div id="donations">
	<p><a href="https://www.buymeacoffee.com/ivijanstefan" target="_blank"><img src="https://img.buymeacoffee.com/button-api/?text=<?php esc_attr_e('Buy me a coffee', 'serbian-transliteration'); ?>&emoji=&slug=ivijanstefan&button_colour=FFDD00&font_colour=000000&font_family=Cookie&outline_colour=000000&coffee_colour=ffffff" /></a></p>
</div>
	<?php }
	
	public function postbox_infinitum(){ ?>
<div class="postbox">
	<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span>INFINITUM FORM®</span></h3><hr>
	<div class="inside">
	<?php
		printf('<p>%s</p>', __('Hire professional developers, designers, SEO masters and marketing ninjas in one place.', 'serbian-transliteration'));
		printf('<p><a href="%1$s" target="_blank">%2$s</a></p>', 'https://infinitumform.com/', __('Read more...', 'serbian-transliteration'));
	?>
	</div>
</div>
	<?php }
	
		public function postbox_buymeacoffee() { ?>
<script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="ivijanstefan" data-description="<?php esc_attr_e('Support my work by buying me a coffee!', 'serbian-transliteration'); ?>" data-message="<?php esc_attr_e('Thank you for using Transliterator. Could you buy me a coffee?', 'serbian-transliteration'); ?>" data-color="#FF813F" data-position="Right" data-x_margin="18" data-y_margin="18"></script>
		<?php }
	
		public function postbox_cloud_hosting(){ ?>
<div class="postbox">
	<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span><?php _e('Need CLOUD HOSTING ???', 'serbian-transliteration'); ?></span></h3><hr>
	<div class="inside">
	<?php
		printf('<p>%s</p>', __('If you need hosting for your personal needs, for business, any web applications, cloud or dedicated servers, we have the ideal solution for you!', 'serbian-transliteration'));
		printf('<p><a href="%1$s" target="_blank">%2$s</a></p>', 'https://portal.draxhost.com/?affid=1', __('Read more...', 'serbian-transliteration'));
	?>
	</div>
</div>
	<?php }
	
		public function postbox_contributors(){
			if($plugin_info = Serbian_Transliteration_Utilities::plugin_info(array('contributors' => true, 'donate_link' => true))) : ?>
<div class="postbox" id="contributors">
	<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span class="dashicons dashicons-superhero-alt"></span> <span><?php _e('Contributors & Developers', 'serbian-transliteration'); ?></span></h3><hr>
	<div class="inside flex">
		<?php foreach($plugin_info->contributors as $username => $info) : $info = (object)$info; ?>
		<div class="contributor contributor-<?php echo esc_attr($username); ?>" id="contributor-<?php echo esc_attr($username); ?>">
			<a href="<?php echo esc_url($info->profile); ?>" target="_blank">
				<img src="<?php echo esc_url($info->avatar); ?>">
				<h3><?php echo esc_html($info->display_name); ?></h3>
			</a>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="inside">
		<?php printf('<p>%s</p>', sprintf(__('If you want to support our work and effort, if you have new ideas or want to improve the existing code, %s.', 'serbian-transliteration'), '<a href="https://github.com/CreativForm/serbian-transliteration" target="_blank">' . __('join our team', 'serbian-transliteration') . '</a>')); ?>
		<?php /* printf('<p>%s</p>', sprintf(__('If you want to help further plugin development, you can also %s.', 'serbian-transliteration'), '<a href="' . esc_url($plugin_info->donate_link) . '" target="_blank">' . __('donate something for effort', 'serbian-transliteration') . '</a>')); */ ?>
	</div>
</div>
<?php endif;
	}
	
}
endif;