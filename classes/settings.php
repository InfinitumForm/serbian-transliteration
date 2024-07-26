<?php if ( !defined('WPINC') ) die();

if( !class_exists('Transliteration_Settings', false) ) : class Transliteration_Settings extends Transliteration {
	
	public function __construct() {
		add_action('admin_menu', array($this, 'add_settings_page'));
		add_action('admin_init', array($this, 'register_settings'));
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

	public function create_admin_page() {
		?>
		<div class="wrap">
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
		<?php
	}

	public function register_settings() {
		$settings_fields = new Transliteration_Settings_Fields;
		$settings_fields->register_settings();
	}

} endif;