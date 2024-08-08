<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Settings', false) ) : class Transliteration_Settings extends Transliteration {
	
	public function __construct() {
		$this->add_action('admin_menu', 'add_settings_page');
		$this->add_action('admin_init', 'register_settings');
		$this->add_action('wp_before_admin_bar_render', 'admin_bar_link');
		$this->add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
	}

	public function add_settings_page() {
		add_options_page(
            __('Transliteration Settings', 'serbian-transliteration'),
            __('Transliteration', 'serbian-transliteration'),
            'manage_options',
            'transliteration-settings',
            array($this, 'create_admin_page')
        );
	}
	
	public function admin_bar_link() {
		if(current_user_can('administrator')) {
			global $wp_admin_bar;
			$wp_admin_bar->add_menu( array(
				'parent' => 'site-name',
				'id' => 'transliteration-settings',
				'title' => __('Transliteration', 'serbian-transliteration'),
				'href' => admin_url( '/options-general.php?page=transliteration-settings' ),
			));
		}
	}
	
	public function enqueue_admin_scripts($hook) {
        // Enqueue scripts only on the transliteration settings page
        if ($hook !== 'settings_page_transliteration-settings') {
            return;
        }

        // Register the CSS and JS files
        wp_register_style('transliteration-admin', RSTR_ASSETS . '/css/admin.css?m=' . filemtime(RSTR_ROOT.'/assets/css/admin.css'));
        wp_register_script('transliteration-admin', RSTR_ASSETS . '/js/admin.js?m=' . filemtime(RSTR_ROOT.'/assets/js/admin.js'), array('jquery'), null, true);
		
		wp_register_style('transliteration-highlight', RSTR_ASSETS . '/css/highlight.min.css');
        wp_register_script('transliteration-highlight', RSTR_ASSETS . '/js/highlight.min.js', array('jquery'), null, true);

        // Enqueue the CSS and JS files
        wp_enqueue_style('transliteration-admin');
        wp_enqueue_script('transliteration-admin');
		
		// Localize script
		wp_localize_script(
			'transliteration-admin',
			'RSTR',
			array(
				'version' => RSTR_VERSION,
				'home' => get_bloginfo('wpurl'),
				'ajax' => admin_url( '/admin-ajax.php' ),
				'prefix' => RSTR_PREFIX,
				'label' => array(
					'progress_loading' => __('Please wait! Do not close the window or leave the page until this operation is completed!', 'serbian-transliteration'),
					'done' => __('DONE!!!', 'serbian-transliteration'),
					'loading' => __('Loading...', 'serbian-transliteration')
				)
			)
		);
    }

	public function create_admin_page() {
		$tab = sanitize_text_field($_GET['tab'] ?? '');		
		switch($tab) {
			default:
			case 'general':
				$this->settings_page__general();
				break;
				
			case 'documentation':
				$this->settings_page__documentation([
					'shortcodes' => __('Shortcodes', 'serbian-transliteration'),
					'functions' => __('PHP Functions', 'serbian-transliteration'),
					'tags' => __('Tags', 'serbian-transliteration')
				]);
				break;
			
			case 'debug':
				$this->settings_page__debug();
				break;
			
			case 'credits':
				$this->settings_page__credits();
				break;
		}
	}
	
	public function settings_tabs() {
		$main_url = admin_url('/options-general.php?page=transliteration-settings');
		$tab = sanitize_text_field($_GET['tab'] ?? 'general');
		$tabs = [
			'general' => [
				'icon' => 'admin-settings',
				'label' => __('General Settings', 'serbian-transliteration')
			],
			'documentation' => [
				'icon' => 'media-spreadsheet',
				'label' => __('Documentation', 'serbian-transliteration')
			],
			'debug' => [
				'icon' => 'sos',
				'label' => __('Debug', 'serbian-transliteration')
			],
			'credits' => [
				'icon' => 'info-outline',
				'label' => __('Credits', 'serbian-transliteration')
			],
		];
		?>
		<ul class="transliteration-settings-tabs">
			<?php foreach ($tabs as $key => $item): ?>
			<li<?php echo (($tab == $key) ? ' class="current"' : ''); ?>>
				<a href="<?php echo esc_url(add_query_arg('tab', $key, $main_url)); ?>"><?php if($item['icon']??NULL) : ?><i class="dashicons dashicons-<?php echo esc_attr($item['icon']); ?>"></i> <?php endif; ?><span class="lbl"><?php echo esc_html($item['label']??''); ?></span></a>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
	
	public function settings_page_actions($actions=[]) {
		$tab = sanitize_text_field($_GET['tab'] ?? 'general');
		$main_url = admin_url('/options-general.php?page=transliteration-settings&tab='.$tab);
		$action = sanitize_text_field($_GET['action'] ?? '');
		?>
		<ul class="transliteration-action-links">
			<?php $i=0; foreach ($actions as $key => $label): ?>
			<li<?php echo (($action == $key || ($i === 0 && empty($action))) ? ' class="active"' : ''); ?>>
				<a href="<?php echo esc_url(add_query_arg('action', $key, $main_url)); ?>"><?php echo esc_html($label); ?></a>
			</li>
			<?php ++$i; endforeach; ?>
		</ul>
		<?php
	}
	
	public function settings_page__general() {
		?>
<div class="wrap" id="wp-transliteration-settings">
	<h1><?php _e('Transliteration', 'serbian-transliteration'); ?></h1>
	<?php $this->settings_tabs(); ?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables">
				<?php do_meta_boxes('transliteration-settings', 'side', null); ?>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<form method="post" action="options.php">
					<?php
					settings_fields('transliteration-group');
					do_settings_sections('serbian-transliteration');
					submit_button();
					?>
				</form>
			</div>
		</div>
	</div>
</div>
		<?php
	}
	
	public function settings_page__documentation($actions) {
		$action = sanitize_text_field($_GET['action'] ?? 'shortcodes');
		
		wp_enqueue_style('transliteration-highlight');
        wp_enqueue_script('transliteration-highlight');

		switch($action) {
			default:
			case 'shortcodes':
				$this->settings_action_page__shortcodes($actions);
				break;
				
			case 'functions':
				$this->settings_action_page__functions($actions);
				break;
			
			case 'tags':
				$this->settings_action_page__tags($actions);
				break;
		}
	}
	
	public function settings_action_page__shortcodes($actions) {
		?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('.lang-txt').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="wrap" id="wp-transliteration-settings">
	<h1><?php _e('Transliteration', 'serbian-transliteration'); ?> - <?php _e('Available shortcodes', 'serbian-transliteration'); ?></h1>
	<?php $this->settings_tabs(); ?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables">
				<?php do_meta_boxes('transliteration-settings', 'side', null); ?>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<div class="transliteration-page" id="documentation-page"><?php
					$this->settings_page_actions($actions);
					include_once RSTR_CLASSES . '/settings/page-shortcodes.php';
				?></div>
			</div>
		</div>
	</div>
</div>
		<?php
	}
	
	public function settings_action_page__functions($actions) {
		?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('code.lang-php').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="wrap" id="wp-transliteration-settings">
	<h1><?php _e('Transliteration', 'serbian-transliteration'); ?> - <?php _e('Available PHP Functions', 'serbian-transliteration'); ?></h1>
	<?php $this->settings_tabs(); ?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables">
				<?php do_meta_boxes('transliteration-settings', 'side', null); ?>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<div class="transliteration-page"><?php
					$this->settings_page_actions($actions);
					include_once RSTR_CLASSES . '/settings/page-functions.php';
				?></div>
			</div>
		</div>
	</div>
</div>
		<?php
	}
	
	public function settings_action_page__tags($actions) {
		?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('.lang-txt').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="wrap" id="wp-transliteration-settings">
	<h1><?php _e('Transliteration', 'serbian-transliteration'); ?> - <?php _e('Available Tags', 'serbian-transliteration'); ?></h1>
	<?php $this->settings_tabs(); ?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables">
				<?php do_meta_boxes('transliteration-settings', 'side', null); ?>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<div class="transliteration-page"><?php
					$this->settings_page_actions($actions);
					include_once RSTR_CLASSES . '/settings/page-tags.php';
				?></div>
			</div>
		</div>
	</div>
</div>
		<?php
	}
	
	public function settings_page__debug() {
		?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('.lang-php').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="wrap" id="wp-transliteration-settings">
	<h1><?php _e('Transliteration', 'serbian-transliteration'); ?> - <?php _e('Debug information', 'serbian-transliteration'); ?></h1>
	<?php $this->settings_tabs(); ?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables">
				<?php do_meta_boxes('transliteration-settings', 'side', null); ?>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<div class="transliteration-page"><?php
					include_once RSTR_CLASSES . '/settings/page-debug.php';
				?></div>
			</div>
		</div>
	</div>
</div>
		<?php
	}
	
	public function settings_page__credits() {
		?>
<div class="wrap" id="wp-transliteration-settings">
	<h1><?php _e('Transliteration', 'serbian-transliteration'); ?> - <?php _e('Credits', 'serbian-transliteration'); ?></h1>
	<?php $this->settings_tabs(); ?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables">
				<?php do_meta_boxes('transliteration-credits', 'side', null); ?>
				<script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="ivijanstefan" data-description="<?php esc_attr_e('Support my work by buying me a coffee!', 'serbian-transliteration'); ?>" data-message="<?php esc_attr_e('Thank you for using Transliterator. Could you buy me a coffee?', 'serbian-transliteration'); ?>" data-color="#FF813F" data-position="Right" data-x_margin="18" data-y_margin="50"></script>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<div class="transliteration-page"><?php
					include_once RSTR_CLASSES . '/settings/page-credits.php';
				?></div>
			</div>
		</div>
	</div>
</div>
		<?php
	}

	public function register_settings() {
		$settings_fields = new Transliteration_Settings_Fields;
		$settings_fields->register_settings();
	}

} endif;