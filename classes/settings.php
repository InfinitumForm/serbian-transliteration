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
		?>
<div class="wrap" id="wp-transliteration-settings">
	<h1><?php _e('Transliteration', 'serbian-transliteration'); ?></h1>
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

	public function register_settings() {
		$settings_fields = new Transliteration_Settings_Fields;
		$settings_fields->register_settings();
	}

} endif;