<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * WP Admin Settings Page
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */

// Include codes
include_once RSTR_INC . '/settings/sidebar.php';
include_once RSTR_INC . '/settings/content.php';

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
		$this->add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );
		$this->add_action( 'plugin_action_links_' . RSTR_BASENAME, 'action_links' );
		$this->add_action( 'plugin_row_meta', 'row_meta_links', 10, 2);
    }
	
	function action_links( $links ) {

		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration' ) ) . '">' . __( 'Settings', RSTR_NAME ) . '</a>',
			'<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration&tab=permalink_tool' ) ) . '">' . __( 'Permalink Tool', RSTR_NAME ) . '</a>'
		), $links );

		return $links;

	}
	
	function row_meta_links( $links, $file ) {
		if ( RSTR_BASENAME == $file ) {
			return array_merge( $links, array(
				'rstr-shortcodes' => '<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration&tab=shortcodes' ) ) . '">' . __( 'Shortcodes', RSTR_NAME ) . '</a>',
				'rstr-functions' => '<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration&tab=functions' ) ) . '">' . __( 'PHP Functions', RSTR_NAME ) . '</a>',
				'rstr-review' => '<a href="https://wordpress.org/support/plugin/serbian-transliteration/reviews/?filter=5#new-post" target="_blank">' . __( '5 stars?', RSTR_NAME ) . '</a>'
			));
		}
		return $links;
	}
	
	/**
     * Enqueue scripts
     */
	public function enqueue_scripts () {
		wp_register_style( RSTR_NAME, RSTR_ASSETS . '/css/serbian-transliteration.css', 1, (string)RSTR_VERSION );
		wp_register_script( RSTR_NAME, RSTR_ASSETS . '/js/serbian-transliteration.js', 1, (string)RSTR_VERSION, true );
		wp_localize_script(
			RSTR_NAME,
			'RSTR',
			array(
				'version' => RSTR_VERSION,
				'home' => get_bloginfo('wpurl'),
				'ajax' => admin_url( '/admin-ajax.php' ),
				'prefix' => RSTR_PREFIX,
				'label' => array(
					'progress_loading' => __('Please wait! Do not close the window or leave the page until this operation is completed!', RSTR_NAME),
					'done' => __('DONE!!!', RSTR_NAME)
				)
			)
		);
		// https://highlightjs.org/
		wp_register_style( 'highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@10.2.0/build/styles/default.min.css', 1, (string)RSTR_VERSION );
		wp_register_script( 'highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@10.2.0/build/highlight.min.js', 1, (string)RSTR_VERSION, true );
	}

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        $this->add_options_page(
            __('Transliteration Settings', RSTR_NAME),
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
		wp_enqueue_style( 'serbian-transliteration');
		wp_enqueue_script('serbian-transliteration');
        // Set class property
        $this->options = get_option( RSTR_NAME );
        ?>
        <div class="wrap" id="<?php echo RSTR_NAME; ?>-settings">
            <h1><?php _e('Transliteration', RSTR_NAME); ?></h1>
			<?php do_action('rstr/settings/content', $this); ?>
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
            __('Global Settings', RSTR_NAME), // Title
            'print_global_settings_callback', // Callback
            RSTR_NAME // Page
        );
		
		$this->add_settings_field(
            'site-script', // ID
            __('My site is', RSTR_NAME), // Title 
            'site_script_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
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
            'allow-cyrillic-usernames', // ID
            __('Allow Cyrillic Usernames', RSTR_NAME), // Title 
            'allow_cyrillic_usernames_callback', // Callback
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
		
		$this->add_settings_field(
            'exclude-latin-words', // ID
            __('Exclude Latin words that you do not want to be transliterated to the Cyrillic.', RSTR_NAME), // Title 
            'exclude_latin_words_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
        );
		
		$this->add_settings_field(
            'exclude-cyrillic-words', // ID
            __('Exclude Cyrillic words that you do not want to be transliterated to the Latin.', RSTR_NAME), // Title 
            'exclude_cyrillic_words_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-global' // Section           
        );
		
		$this->register_setting(
            RSTR_NAME . '-search', // Option group
            RSTR_NAME, // Option name
            'sanitize' // Sanitize
        );
		
		$this->add_settings_section(
            RSTR_NAME . '-search', // ID
            __('WordPress search', RSTR_NAME), // Title
            'print_search_settings_callback', // Callback
            RSTR_NAME // Page
        );
		
		$this->add_settings_field(
            'enable-search', // ID
            __('Enable search transliteration', RSTR_NAME), // Title 
            'enable_search_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-search' // Section           
        );
		
		/*$this->add_settings_field(
            'search-mode', // ID
            __('Search mode', RSTR_NAME), // Title 
            'search_mode_callback', // Callback
            RSTR_NAME, // Page
            RSTR_NAME . '-search' // Section           
        );*/
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

		foreach($input as $key=>$value)
		{
			if(is_array($value)) {
				$new_input[$key] = array_map('sanitize_text_field', $value);
			} else {
				$new_input[$key] = sanitize_text_field($value);
			}
		}

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_global_settings_callback()
    {
        printf('<p>%s</p>', __('This setting determines the mode of operation for the Serbian Transliteration plugin.', RSTR_NAME));
		printf('<p>%s</p>', __('Carefully choose the option that is best for your site and the plugin will automatically set everything you need for optimal performance.', RSTR_NAME));
    }
	
	public function print_search_settings_callback()
	{
		printf('<p>%s</p>', __('This setting determines the search mode within the WordPress core depending on the type of language located in the database.', RSTR_NAME));
		printf('<p>%s</p>', __('The search type setting is mostly experimental and you need to test each variant so you can get the best result you need.', RSTR_NAME));
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
				(isset( $this->options['mode'] ) ? ($this->options['mode'] == $key ? ' checked' : '') : ($key == 'standard' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join('<br>', $inputs), __('Forced transliteration can sometimes cause problems if Latin is translated into Cyrillic in pages and posts. To this combination must be approached experimentally.', RSTR_NAME));
	}
	
	public function site_script_callback(){
		$inputs = array();
		
		foreach(array(
			'cyr' => __('Cyrillic', RSTR_NAME),
			'lat' => __('Latin', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="site-script-%1$s"><input type="radio" id="site-script-%1$s" name="%3$s[site-script]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['site-script'] ) ? ($this->options['site-script'] == $key ? ' checked' : '') : ($key == 'cyr' ? ' checked' : ''))
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
				(isset( $this->options['transliteration-mode'] ) ? ($this->options['transliteration-mode'] == $key ? ' checked' : '') : ($key == 'none' ? ' checked' : ''))
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
				(isset( $this->options['avoid-admin'] ) ? ($this->options['avoid-admin'] == $key ? ' checked' : '') : ($key == 'yes' ? ' checked' : ''))
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
				(isset( $this->options['media-transliteration'] ) ? ($this->options['media-transliteration'] == $key ? ' checked' : '') : ($key == 'yes' ? ' checked' : ''))
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
	
	public function exclude_latin_words_callback()
	{
		printf(
			'<textarea name="%1$s[exclude-latin-words]" id="%1$s-exclude-latin-words" rows="5" style="width:100%%">%2$s</textarea>',
			RSTR_NAME,
			(isset( $this->options['exclude-latin-words'] ) ? esc_html($this->options['exclude-latin-words']) : '')
		);
		printf('<p class="description">%1$s</p>', sprintf(__('Separate words, phrases, names and expressions with the sign %s or put it in a new row. HTML is not allowed.', RSTR_NAME), '<code>|</code>'));
	}
	
	public function exclude_cyrillic_words_callback()
	{
		printf(
			'<textarea name="%1$s[exclude-cyrillic-words]" id="%1$s-exclude-cyrillic-words" rows="5" style="width:100%%">%2$s</textarea>',
			RSTR_NAME,
			(isset( $this->options['exclude-cyrillic-words'] ) ? esc_html($this->options['exclude-cyrillic-words']) : '')
		);
		printf('<p class="description">%1$s</p>', sprintf(__('Separate words, phrases, names and expressions with the sign %s or put it in a new row. HTML is not allowed.', RSTR_NAME), '<code>|</code>'));
	}
	
	public function allow_cyrillic_usernames_callback()
	{
		$inputs = array();
		
		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="enable-search-%1$s"><input type="radio" id="enable-search-%1$s" name="%3$s[allow-cyrillic-usernames]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['allow-cyrillic-usernames'] ) ? ($this->options['allow-cyrillic-usernames'] == $key ? ' checked' : '') : ($key == 'no' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('Allows to create users with usernames containing Cyrillic characters.', RSTR_NAME));
	}
	
	public function enable_search_callback()
	{
		$inputs = array();
		
		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="enable-search-%1$s"><input type="radio" id="enable-search-%1$s" name="%3$s[enable-search]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['enable-search'] ) ? ($this->options['enable-search'] == $key ? ' checked' : '') : ($key == 'no' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('Approve if you want transliteration for the search field.', RSTR_NAME));
	}
	
	/*public function search_mode_callback()
	{
		$inputs = array();
		
		foreach(array(
			'lat_to_cyr' => __('Enable Latin search on the Cyrillic site', RSTR_NAME),
			'cyr_to_lat' => __('Enable Cyrillic search on the Latin site', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="search-mode-%1$s"><input type="radio" id="search-mode-%1$s" name="%3$s[search-mode]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['search-mode'] ) ? ($this->options['search-mode'] == $key ? ' checked' : '') : ($key == 'lat_to_cyr' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join('<br>', $inputs), __('Select the search mode according to your WordPress setup.', RSTR_NAME));
	}*/
}
endif;