<?php
/**
 * WP Admin Settings Page
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Settings')) :
class Serbian_Transliteration_Settings extends Serbian_Transliteration
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        $this->add_action( 'admin_menu', 'add_plugin_page' );
        $this->add_action( 'admin_init', 'page_init' );
		
		
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        $this->add_options_page(
            __('Serbian Transliteration', RSTR_NAME),
            __('Transliteration', RSTR_NAME), 
            'manage_options', 
            RSTR_NAME, 
            'create_admin_page'
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( RSTR_NAME );
        ?>
        <div class="wrap">
            <h1><?php _e('Serbian Transliteration', RSTR_NAME); ?></h1>

			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<!-- BOXES -->
						<div class="postbox">
							<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span><?php _e('Available shortcodes', RSTR_NAME); ?></span></h3><hr>
							<div class="inside">
								<h4 style="margin:0;"><?php _e('Cyrillic to Latin', RSTR_NAME); ?>:</h4>
								<code>[rstr_cyr_to_lat]Ћирилица у латиницу[/rstr_cyr_to_lat]</code>
								<br><br>
								<h4 style="margin:0;"><?php _e('Latin to Cyrillic', RSTR_NAME); ?>:</h4>
								<code>[rstr_lat_to_cyr]Latinica u ćirilicu[/rstr_lat_to_cyr]</code>
							</div>
						</div>
						
						<div class="postbox">
							<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span>INFINITUM FORM®</span></h3><hr>
							<div class="inside">
							<?php
								printf('<p>%s</p>', __('Hire professional developers, designers, SEO masters and marketing ninjas in one place.', RSTR_NAME));
								printf('<p><a href="%1$s" target="_blank">%2$s</a></p>', 'https://infinitumform.com/', __('Read more...', RSTR_NAME));
							?>
							</div>
						</div>
						
						<div class="postbox">
							<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span><?php _e('Need CLOUD HOSTING ???', RSTR_NAME); ?></span></h3><hr>
							<div class="inside">
							<?php
								printf('<p>%s</p>', __('If you need hosting for your personal needs, for business, any web applications, cloud or dedicated servers, we have the ideal solution for you!', RSTR_NAME));
								printf('<p><a href="%1$s" target="_blank">%2$s</a></p>', 'https://www.draxhost.com/checkout/aff.php?aff=1', __('Read more...', RSTR_NAME));
							?>
							</div>
						</div>
						
					</div>
				</div>
			 
				<div id="post-body">
					<div id="post-body-content">
						<form method="post" action="<?php echo esc_url(admin_url('/options.php')); ?>">
						<?php
							// This prints out all hidden setting fields
							settings_fields( RSTR_NAME . '-group' );
							do_settings_sections( RSTR_NAME );
							submit_button();
						?>
						</form>
					</div>
				</div>
				<br class="clear">
			</div>

        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        $this->register_setting(
            RSTR_NAME . '-group', // Option group
            RSTR_NAME, // Option name
            'sanitize' // Sanitize
        );

        $this->add_settings_section(
            RSTR_NAME . '-global', // ID
            '', // Title
            'print_section_info', // Callback
            RSTR_NAME // Page
        );
		
		$this->add_settings_field(
            'transliteration-mode', // ID
            __('Transliteration Mode', RSTR_NAME), // Title 
            'transliteration_mode_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
        );

        $this->add_settings_field(
            'mode', // ID
            __('Plugin Mode', RSTR_NAME), // Title 
            'mode_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
        );
		
		$this->add_settings_field(
            'avoid-admin', // ID
            __('Disabled inside wp-admin', RSTR_NAME), // Title 
            'avoid_admin_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
        );
		
		$this->add_settings_field(
            'media-transliteration', // ID
            __('Transliterate filenames to latin', RSTR_NAME), // Title 
            'media_transliteration_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
        );
		
		$this->add_settings_field(
            'permalink-transliteration', // ID
            __('Force transliteration permalinks to latin', RSTR_NAME), // Title 
            'permalink_transliteration_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        printf('<p>%s</p>', __('This setting determines the mode of operation for the Serbian Transliteration plugin.', RSTR_NAME));
		printf('<p>%s</p>', __('Carefully choose the option that is best for your site and the plugin will automatically set everything you need for optimal performance.', RSTR_NAME));
    }

    /** 
     * Plugin mode
     */
    public function mode_callback()
    {
		$inputs = array();
		
		foreach($this->plugin_mode() as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="mode-%1$s"><input type="radio" id="mode-%1$s" name="%3$s[mode]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['mode'] ) && $this->options['mode'] == $key ? ' checked' : ($key == 'standard' ? ' checked' : ''))
			);
		}
		
        echo join('<br>', $inputs);
	}
	
	/** 
     * Transliteration mode
     */
    public function transliteration_mode_callback()
    {
		$inputs = array();
		
		foreach($this->transliteration_mode() as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="transliteration-mode-%1$s"><input type="radio" id="transliteration-mode-%1$s" name="%3$s[transliteration-mode]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['transliteration-mode'] ) && $this->options['transliteration-mode'] == $key ? ' checked' : ($key == 'none' ? ' checked' : ''))
			);
		}
		
        echo join('<br>', $inputs);
	}
	
	/** 
     * Avoid admin
     */
    public function avoid_admin_callback()
    {
		$inputs = array();
		
		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="avoid-admin-%1$s"><input type="radio" id="avoid-admin-%1$s" name="%3$s[avoid-admin]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['avoid-admin'] ) && $this->options['avoid-admin'] == $key ? ' checked' : ($key == 'yes' ? ' checked' : ''))
			);
		}
		
        echo join(' ', $inputs);
	}
	
	/** 
     * Transliterate filenames to latin
     */
	public function media_transliteration_callback()
	{
		$inputs = array();
		
		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="media-transliteration-%1$s"><input type="radio" id="media-transliteration-%1$s" name="%3$s[media-transliteration]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['media-transliteration'] ) && $this->options['media-transliteration'] == $key ? ' checked' : ($key == 'yes' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('Enable if you want to convert cyrillic filenames to latin.', RSTR_NAME));
	}
	
	/** 
     * Force transliteration permalinks to latin
     */
	public function permalink_transliteration_callback()
	{
		$inputs = array();
		
		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="permalink-transliteration-%1$s"><input type="radio" id="permalink-transliteration-%1$s" name="%3$s[permalink-transliteration]" value="%1$s"%4$s%5$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['permalink-transliteration'] ) && $this->options['permalink-transliteration'] == $key ? ' checked' : (($this->get_locale() == 'sr_RS' && get_option('ser_cyr_to_lat_slug') ? $key == 'no' : $key == 'yes') ? ' checked' : '')),
				($this->get_locale() == 'sr_RS' && get_option('ser_cyr_to_lat_slug') ? ' disabled' : '')
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('Enable if you want to force cyrillic permalinks to latin.', RSTR_NAME));
		if($this->get_locale() == 'sr_RS' && get_option('ser_cyr_to_lat_slug')) {
			printf('<p class="description"><b>%1$s</b></p>', sprintf(__('You don\'t need to force transliteration permalinks to latin because your current locale is set to %s which will automatically change permalnks.', RSTR_NAME), '<code>'.$this->get_locale().'</code>'));
		}
	}
}
endif;