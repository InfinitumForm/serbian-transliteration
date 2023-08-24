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
		$this->add_action('rstr/settings/sidebar/tab/credits', 'postbox_donations');
		
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
<div class="postbox">
	<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span class="dashicons dashicons-heart"></span> <span><?php _e('Donations', 'serbian-transliteration'); ?></span></h3><hr>
	<div class="inside">
	<?php printf('<p>%s</p>', __('This plugin is 100% free. If you want to buy us one coffee, beer or in general help the development of this plugin through a monetary donation, you can do it in the following ways:', 'serbian-transliteration')); ?>
	<ul>
		<?php printf('<li><b>%s</b>: %s</li>', __('PayPal', 'serbian-transliteration'), 'creativform@gmail.com'); ?>
		<?php printf('<li><b>%s</b>: %s (%s)</li>', __('From Serbia', 'serbian-transliteration'), '115-0000000138835-77', __('Mobi Bank', 'serbian-transliteration')); ?>
	</ul>
	</div>
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
		<?php printf('<p>%s</p>', sprintf(__('If you want to help further plugin development, you can also %s.', 'serbian-transliteration'), '<a href="' . esc_url($plugin_info->donate_link) . '" target="_blank">' . __('donate something for effort', 'serbian-transliteration') . '</a>')); ?>
	</div>
</div>
<?php endif;
	}
	
}
endif;