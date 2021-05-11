<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * WP Admin Settings Page
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */

// Include codes
if(file_exists(RSTR_INC . '/settings/sidebar.php')) {
	include_once RSTR_INC . '/settings/sidebar.php';
}
if(file_exists(RSTR_INC . '/settings/content.php')) {
	include_once RSTR_INC . '/settings/content.php';
}

if(!class_exists('Serbian_Transliteration_Settings')) :
class Serbian_Transliteration_Settings extends Serbian_Transliteration
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	private $nonce;

    /**
     * Start up
     */
    public function __construct()
    {
		global $rstr_cache;

		if(current_user_can('administrator'))
		{
			$this->add_action( 'admin_menu', 'add_plugin_page' );
			$this->add_action( 'admin_init', 'page_init' );
			$this->add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );
			$this->add_action( 'plugin_action_links_' . RSTR_BASENAME, 'action_links' );
			$this->add_action( 'plugin_row_meta', 'row_meta_links', 10, 2);

			$this->nonce = esc_attr(wp_create_nonce('rstr-options'));

			if($mode_class = Serbian_Transliteration_Utilities::mode()) {
				$rstr_cache->set('Serbian_Transliteration_Settings__active_filters', array_keys($mode_class::filters()));
			}

			if($active_plugins = Serbian_Transliteration_Plugins::includes(array(), true)->active_filters())
			{
				$active_plugins = array_keys($active_plugins);
				$rstr_cache->set('Serbian_Transliteration_Settings__active_filters', array_merge($rstr_cache->get('Serbian_Transliteration_Settings__active_filters'), $active_plugins));
			}

			if($active_themes = Serbian_Transliteration_Themes::includes(array(), true)->active_filters())
			{
				$active_themes = array_keys($active_themes);
				$rstr_cache->set('Serbian_Transliteration_Settings__active_filters', array_merge($rstr_cache->get('Serbian_Transliteration_Settings__active_filters'), $active_themes));
			}

			$this->add_action( 'wp_ajax_rstr_filter_mode_options', 'ajax__rstr_filter_mode_options');
		}
    }

	public function ajax__rstr_filter_mode_options( ) {
		global $rstr_cache;
		$mode_class = Serbian_Transliteration_Utilities::mode(array('mode'=>sanitize_text_field($_POST['mode'])));

		if($mode_class !== false && isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'rstr-options') !== false)
		{

			if(empty($this->options)){
				$this->options = get_rstr_option();
			}

			$this->options['mode'] = sanitize_text_field($_POST['mode']);

			$list = array_keys($mode_class::filters());
			if($active_plugins = Serbian_Transliteration_Plugins::includes($this->options, true)->active_filters())
			{
				$active_plugins = array_keys($active_plugins);
				$list = array_merge($list, $active_plugins);
			}

			if($active_themes = Serbian_Transliteration_Themes::includes($this->options, true)->active_filters())
			{
				$active_themes = array_keys($active_themes);
				$rstr_cache->set('Serbian_Transliteration_Settings__active_filters', array_merge($rstr_cache->get('Serbian_Transliteration_Settings__active_filters'), $active_themes));
			}

			$this->private___list_filter_mode_options($list, $this->options);
		}
		exit;
	}

	public function action_links( $links ) {

		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration' ) ) . '">' . __( 'Settings', RSTR_NAME ) . '</a>',
			'<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration&tab=tools&action=permalink_tool' ) ) . '">' . __( 'Permalink Tool', RSTR_NAME ) . '</a>'
		), $links );

		return $links;

	}

	public function row_meta_links( $links, $file ) {
		if ( RSTR_BASENAME == $file ) {
			return array_merge( $links, array(
				'rstr-shortcodes' => '<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration&tab=documentation&action=shortcodes' ) ) . '">' . __( 'Shortcodes', RSTR_NAME ) . '</a>',
				'rstr-functions' => '<a href="' . esc_url( admin_url( '/options-general.php?page=serbian-transliteration&tab=documentation&action=functions' ) ) . '">' . __( 'PHP Functions', RSTR_NAME ) . '</a>',
				'rstr-review' => '<a href="https://wordpress.org/support/plugin/serbian-transliteration/reviews/?filter=5#new-post" target="_blank">' . __( '5 stars?', RSTR_NAME ) . '</a>'
			));
		}
		return $links;
	}

	/**
     * Enqueue scripts
     */
	public function enqueue_scripts () {

		if(defined( 'RSTR_DEBUG' ) && RSTR_DEBUG) {
			wp_register_style( RSTR_NAME, RSTR_ASSETS . '/css/serbian-transliteration.css', array('common'), (string)RSTR_VERSION );
			wp_register_script( RSTR_NAME, RSTR_ASSETS . '/js/serbian-transliteration.js', 1, (string)RSTR_VERSION, true );
		} else {
			wp_register_style( RSTR_NAME, RSTR_ASSETS . '/css/serbian-transliteration.min.css', array('common'), (string)RSTR_VERSION );
			wp_register_script( RSTR_NAME, RSTR_ASSETS . '/js/serbian-transliteration.min.js', 1, (string)RSTR_VERSION, true );
		}
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
					'done' => __('DONE!!!', RSTR_NAME),
					'loading' => __('Loading...', RSTR_NAME)
				)
			)
		);
		/*
		 * https://highlightjs.org/
		 * https://github.com/highlightjs/highlight.js
		 */
		wp_register_style( 'highlight', RSTR_ASSETS . '/css/highlight.min.css', array('common'), (string)RSTR_VERSION );
		wp_register_script( 'highlight', RSTR_ASSETS . '/js/highlight.min.js', 1, (string)RSTR_VERSION, true );
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
        if(empty($this->options)){
			$this->options = get_rstr_option();
		}
        ?>
        <div class="wrap" id="<?php echo RSTR_NAME; ?>-settings">
            <h1><img src="<?php echo RSTR_ASSETS . '/img/icon-animated-24x24.gif'; ?>" /> <?php _e('Transliteration', RSTR_NAME); ?></h1>
			<?php do_action('rstr/settings/content', $this); ?>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
		global $rstr_cache;

        $this->register_setting(
			RSTR_NAME . '-group', // Option group
			RSTR_NAME, // Option name
			array(
				'type' => 'array',
				'sanitize_callback' => array($this,'sanitize')
			) // Sanitize
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
				'language-scheme', // ID
				__('Language scheme', RSTR_NAME), // Title
				'language_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-global' // Section
			);
	
			if($rstr_cache->get('Serbian_Transliteration_Settings__active_filters')) {
				$this->add_settings_field(
					'transliteration-filter', // ID
					__('Transliteration Filters', RSTR_NAME), // Title
					'transliteration_filter_callback', // Callback
					RSTR_NAME, // Page
					RSTR_NAME . '-global' // Section
				);
			}
	
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





		$this->add_settings_section(
			RSTR_NAME . '-special-settings', // ID
			__('Special Settings', RSTR_NAME), // Title
			'print_special_settings_callback', // Callback
			RSTR_NAME // Page
		);

			$this->add_settings_field(
				'cache-support', // ID
				__('Enable cache support', RSTR_NAME), // Title
				'cache_support_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-special-settings' // Section
			);

			$this->add_settings_field(
				'force-widgets', // ID
				__('Force widget transliteration', RSTR_NAME), // Title
				'force_widgets_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-special-settings' // Section
			);

			$this->add_settings_field(
				'force-email-transliteration', // ID
				__('Force e-mail transliteration', RSTR_NAME), // Title
				'force_email_transliteration_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-special-settings' // Section
			);





		$this->add_settings_section(
			RSTR_NAME . '-admin', // ID
			__('WP Admin', RSTR_NAME), // Title
			'print_wp_admin_callback', // Callback
			RSTR_NAME // Page
		);

			$this->add_settings_field(
				'avoid-admin', // ID
				__('Disable transliteration inside wp-admin', RSTR_NAME), // Title
				'avoid_admin_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-admin' // Section
			);
	
			$this->add_settings_field(
				'allow-cyrillic-usernames', // ID
				__('Allow Cyrillic Usernames', RSTR_NAME), // Title
				'allow_cyrillic_usernames_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-admin' // Section
			);
	
			$this->add_settings_field(
				'permalink-transliteration', // ID
				__('Force transliteration permalinks to latin', RSTR_NAME), // Title
				'permalink_transliteration_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-admin' // Section
			);





		$this->add_settings_section(
			RSTR_NAME . '-media', // ID
			__('Media Settings', RSTR_NAME), // Title
			'print_media_callback', // Callback
			RSTR_NAME // Page
		);

			$this->add_settings_field(
				'media-transliteration', // ID
				__('Transliterate filenames to latin', RSTR_NAME), // Title
				'media_transliteration_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-media' // Section
			);
	
			$this->add_settings_field(
				'media-delimiter', // ID
				__('Filename delimiter', RSTR_NAME), // Title
				'media_delimiter_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-media' // Section
			);




		$this->register_setting(
			RSTR_NAME . '-search', // Option group
			RSTR_NAME, // Option name
			array(
				'type' => 'array',
				'sanitize_callback' => array($this,'sanitize')
			) // Sanitize
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
	
			if(Serbian_Transliteration::__instance()->get_locale() == 'sr_RS'){
				$this->add_settings_field(
					'fix-diacritics', // ID
					__('Fix Diacritics', RSTR_NAME), // Title
					'fix_diacritics_callback', // Callback
					RSTR_NAME, // Page
					RSTR_NAME . '-search' // Section
				);
			}
	
			$this->add_settings_field(
				'search-mode', // ID
				__('Search Mode', RSTR_NAME), // Title
				'search_mode_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-search' // Section
			);





		$this->add_settings_section(
			RSTR_NAME . '-seo', // ID
			__('SEO Settings', RSTR_NAME), // Title
			'print_seo_settings_callback', // Callback
			RSTR_NAME // Page
		);

			$this->add_settings_field(
				'first-visit-mode', // ID
				__('First visit mode', RSTR_NAME), // Title
				'first_visit_mode_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-seo' // Section
			);
	
			$this->add_settings_field(
				'enable-alternate-links', // ID
				__('Enable alternet links', RSTR_NAME), // Title
				'enable_alternate_links_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-seo' // Section
			);
	
			$this->add_settings_field(
				'parameter-url-selector', // ID
				__('Parameter URL selector', RSTR_NAME), // Title
				'parameter_url_selector_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-seo' // Section
			);
	
			$this->add_settings_field(
				'enable-rss', // ID
				__('RSS transliteration', RSTR_NAME), // Title
				'enable_rss_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-seo' // Section
			);



		$this->add_settings_section(
			RSTR_NAME . '-misc', // ID
			__('Misc.', RSTR_NAME), // Title
			'print_misc_settings_callback', // Callback
			RSTR_NAME // Page
		);

			$this->add_settings_field(
				'enable-body-class', // ID
				__('Enable body class', RSTR_NAME), // Title
				'enable_body_class_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-misc' // Section
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

		foreach($input as $key=>$value)
		{
			if(is_array($value)) {
				$new_input[$key] = array_map('sanitize_text_field', $value);
			} else {
				$new_input[$key] = sanitize_text_field($value);
			}
		}

		if(isset($new_input[RSTR_NAME]['transliteration-mode']))
		{
			switch($new_input[RSTR_NAME]['transliteration-mode'])
			{
				case 'cyr_to_lat':
					Serbian_Transliteration_Utilities::setcookie('lat');
					break;

				case 'lat_to_cyr':
					Serbian_Transliteration_Utilities::setcookie('cyr');
					break;
			}
		}

		Serbian_Transliteration_Utilities::clear_plugin_cache();

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

	public function print_seo_settings_callback()
	{
		printf('<p>%s</p>', __('Our plugin also has special SEO options that are very important for your project.', RSTR_NAME));
	}

	public function print_wp_admin_callback()
	{
		printf('<p>%s</p>', __('These settings apply to the administrative part.', RSTR_NAME));
	}

	public function print_special_settings_callback()
	{
		printf('<p>%s</p>', __('These are special settings that can enhance transliteration and are used only if you need them.', RSTR_NAME));
	}

	public function print_media_callback()
	{
		printf('<p>%s</p>', __('Upload, view and control media and files.', RSTR_NAME));
	}

	public function print_misc_settings_callback()
	{
		printf('<p>%s</p>', __('Various interesting settings that can be used in the development of your project.', RSTR_NAME));
	}


    /**
     * Plugin mode
     */
    public function mode_callback()
    {
		$inputs = array();

		foreach(Serbian_Transliteration_Utilities::plugin_mode() as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="mode-%1$s"><input type="radio" id="mode-%1$s" name="%3$s[mode]" value="%1$s" data-nonce="%4$s"%5$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				$this->nonce,
				(isset( $this->options['mode'] ) ? ($this->options['mode'] == $key ? ' checked' : '') : ($key == 'standard' ? ' checked' : ''))
			);
		}

		printf(
			'<div%3$s id="rstr-mode-list">%1$s<br><p class="description info" id="forced-transliteration" style="display:none; ">%2$s</p></div>',
			join('<br>', $inputs),
			__('Forced transliteration can sometimes cause problems if Latin is translated into Cyrillic in pages and posts. To this combination must be approached experimentally.', RSTR_NAME),
			(get_rstr_option('mode') === 'woocommerce' && RSTR_WOOCOMMERCE === false ? ' class="required-box"' : '')
		);
	}

	public function site_script_callback(){
		$inputs = array();
		$locale = Serbian_Transliteration_Utilities::get_locale();

		$checkbox = array(
			'cyr' => __('Cyrillic', RSTR_NAME),
			'lat' => __('Latin', RSTR_NAME)
		);

		if($locale == 'ar'){
			$checkbox['cyr']= __('Arabic', RSTR_NAME);
		} else if($locale == 'hy'){
			$checkbox['cyr']= __('Armenian', RSTR_NAME);
		}

		foreach($checkbox as $key=>$label)
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

		foreach(Serbian_Transliteration_Utilities::transliteration_mode() as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="transliteration-mode-%1$s"><input type="radio" id="transliteration-mode-%1$s" name="%3$s[transliteration-mode]" value="%1$s" data-nonce="%4$s"%5$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				$this->nonce,
				(isset( $this->options['transliteration-mode'] ) ? ($this->options['transliteration-mode'] == $key ? ' checked' : '') : ($key == 'none' ? ' checked' : ''))
			);
		}

        echo join('<br>', $inputs);
	}

	/**
     * Transliteration language
     */
	public function language_callback(){
		$inputs = array();
		$languages = array_merge(array('auto' => __('Automatical (recommended)', RSTR_NAME)), Serbian_Transliteration_Transliterating::registered_languages());
		foreach($languages as $locale=>$label)
		{
			$inputs[]=sprintf(
				'<option value="%1$s"%3$s>%2$s</option>',
				$locale,
				esc_html($label) . ($locale != 'auto' ? " ({$locale})" : ''),
				(isset( $this->options['language-scheme'] ) ? ($this->options['language-scheme'] == $locale ? ' selected' : '') : ($locale == 'auto' ? ' selected' : ''))
			);
		}
        echo '<select name="'.RSTR_NAME.'[language-scheme]" id="'.RSTR_NAME.'-language-scheme" data-nonce="'.$this->nonce.'" style="margin-bottom:5px;">' . join('<br>', $inputs) . '</select>';

		printf('<br><p class="description">%1$s</p>', __('This option defines the language script. Automatic script detection is the best way but if you are using a WordPress installation in a language that does not match the scripts supported by this plugin, then choose on which script you want the transliteration to be performed.', RSTR_NAME));
	}

	/**
     * Transliteration filter
     */
    public function transliteration_filter_callback()
    {
		global $rstr_cache;
		if(!$rstr_cache->get('Serbian_Transliteration_Settings__active_filters')) return;
?>
<div class="accordion-container">
	<button class="accordion-link" type="button"><?php _e('Exclude filters you don\'t need (optional)', RSTR_NAME); ?></button>
	<div class="accordion-panel">
		<?php

		printf(
			'<p>%s<br><b>%s</b></p><br>',
			__('Select the transliteration filters you want to exclude.', RSTR_NAME),
			__('The filters you select here will not be transliterated (these filters do not work on forced transliteration).', RSTR_NAME)
		);

		$list = $rstr_cache->get('Serbian_Transliteration_Settings__active_filters');

		?>
        <div class="row" id="rstr-filter-mode-options">
			<?php $this->private___list_filter_mode_options($list); ?>
        </div>
		<?php
		printf(
			'<br><p><b>%s</b><br>%s %s</p>',
			__('TIPS & TRICKS:', RSTR_NAME),
			__('You can find details about some of the listed filters in this article:', RSTR_NAME),
			'<a href="https://codex.wordpress.org/Plugin_API/Filter_Reference" target="_blank">Plugin_API/Filter_Reference</a>'
		);

		if(RSTR_WOOCOMMERCE)
		{
			printf(
				'<p>%s</p>',
				sprintf(
					__('Since you are already a WooCommerce user, you also can see the following documentation: %s', RSTR_NAME),
					'<a href="https://docs.woocommerce.com/documentation/plugins/woocommerce/woocommerce-codex/snippets/" target="_blank">WooCommerce/Codex Snippets</a>'
				)
			);
		}

		?>
	</div>
</div>
<?php
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
     * Filename delimiter. Example: `my-upload-file.jpg`
     */
	public function media_delimiter_callback(){
		$inputs = array();

		foreach(array(
			'auto' => __('Automatical (recommended)', RSTR_NAME),
			'-' => '- (' . __('hyphen', RSTR_NAME) . ')',
			'_' => '_  (' . __('underscore', RSTR_NAME) . ')',
			'.' => '. (' . __('dot', RSTR_NAME) . ')',
			'~' => '~ (' . __('tilde', RSTR_NAME) . ')'
		) as $label => $name)
		{
			$inputs[]=sprintf(
				'<option value="%1$s"%3$s>%2$s</option>',
				$label,
				$name,
				(isset( $this->options['media-delimiter'] ) ? ($this->options['media-delimiter'] == $label ? ' selected' : '') : ($label == 'auto' ? ' selected' : ''))
			);
		}
        echo '<select name="'.RSTR_NAME.'[media-delimiter]" id="'.RSTR_NAME.'-media-delimiter" data-nonce="'.$this->nonce.'" style="margin-bottom:5px;">' . join('<br>', $inputs) . '</select>';

		printf('<br><p class="description">%1$s <code>%2$s</code></p>', __('Filename delimiter, example:', RSTR_NAME), __('my-upload-file.jpg', RSTR_NAME));
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


	public function force_widgets_callback(){
		$inputs = array();

		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="force-widgets-%1$s"><input type="radio" id="force-widgets-%1$s" name="%3$s[force-widgets]" value="%1$s"%4$s%5$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['force-widgets'] ) && $this->options['force-widgets'] == $key ? ' checked' : (!isset( $this->options['force-widgets'] ) && $key == 'no' ? ' checked' : '')),
				''
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('This option forces the widget to transliterate. There may be some unusual behaviour in the rare cases.', RSTR_NAME));
	}

	public function enable_rss_callback(){
		$inputs = array();

		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="enable-rss-%1$s"><input type="radio" id="enable-rss-%1$s" name="%3$s[enable-rss]" value="%1$s"%4$s%5$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['enable-rss'] ) && $this->options['enable-rss'] == $key ? ' checked' : (!isset( $this->options['enable-rss'] ) && $key == 'no' ? ' checked' : '')),
				''
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('This option transliterate the RSS feed.', RSTR_NAME));
	}

	public function cache_support_callback(){
		$inputs = array();

		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="cache-support-%1$s"><input type="radio" id="cache-support-%1$s" name="%3$s[cache-support]" value="%1$s"%4$s%5$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['cache-support'] ) && $this->options['cache-support'] == $key ? ' checked' : (!isset( $this->options['cache-support'] ) && $key == 'yes' ? ' checked' : '')),
				''
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('If you have a problem caching your pages, our plugin solves this problem by clearing the cache when changing the language script.', RSTR_NAME));
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


	public function fix_diacritics_callback()
	{
		$inputs = array();

		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="fix-diacritics-%1$s"><input type="radio" id="fix-diacritics-%1$s" name="%3$s[fix-diacritics]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['fix-diacritics'] ) ? ($this->options['fix-diacritics'] == $key ? ' checked' : '') : ($key == 'no' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('Try to fix the diacritics in the search field.', RSTR_NAME));
	}

	public function search_mode_callback()
	{
		$inputs = array();

		foreach(array(
			'auto' => __('Automatical (recommended)', RSTR_NAME),
			'plugin-mode' => __('Based on the plugin mode', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="search-mode-%1$s"><input type="radio" id="search-mode-%1$s" name="%3$s[search-mode]" value="%1$s"%4$s> <span>%2$s</span></label><br>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['search-mode'] ) ? ($this->options['search-mode'] == $key ? ' checked' : '') : ($key == 'auto' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('The search has two working modes. Choose the one that works best with your search.', RSTR_NAME));
	}

	public function enable_alternate_links_callback()
	{
		$inputs = array();

		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="enable-alternate-links-%1$s"><input type="radio" id="enable-alternate-links-%1$s" name="%3$s[enable-alternate-links]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['enable-alternate-links'] ) ? ($this->options['enable-alternate-links'] == $key ? ' checked' : '') : ($key == 'yes' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('Tell Google, Bing, Yandex and other search engines about transliterated versions of your page.', RSTR_NAME));
	}

	public function first_visit_mode_callback()
	{
		$inputs = array();

		foreach(array(
			'auto' => __('Auto (Based on Transliteration Mode)', RSTR_NAME),
			'lat' => __('Latin', RSTR_NAME),
			'cyr' => __('Cyrillic', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="first-visit-mode-%1$s"><input type="radio" id="first-visit-mode-%1$s" name="%3$s[first-visit-mode]" value="%1$s"%4$s> <span>%2$s</span></label><br>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['first-visit-mode'] ) ? ($this->options['first-visit-mode'] == $key ? ' checked' : '') : ($key == 'auto' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('This option determines the type of language script that the visitors sees when they first come to your site.', RSTR_NAME));
	}

	public function parameter_url_selector_callback()
	{
		$inputs = array();

		foreach(array(
			'rstr' => '?<b>rstr</b>=lat ' . __('(safe)', RSTR_NAME),
			'script' => '?<b>script</b>=lat ' . __('(standard)', RSTR_NAME),
			'lang_script' => '?<b>lang_script</b>=lat ' . __('(optional)', RSTR_NAME),
			'letter' => '?<b>letter</b>=lat ' . __('(optional)', RSTR_NAME),
			'skripta' => '?<b>skripta</b>=lat ' . __('(optional)', RSTR_NAME),
			'pismo' => '?<b>pismo</b>=lat ' . __('(optional)', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="first-visit-mode-%1$s"><input type="radio" id="first-visit-mode-%1$s" name="%3$s[url-selector]" value="%1$s"%4$s> <span>%2$s</span></label><br>',
				esc_attr($key),
				$label,
				RSTR_NAME,
				(isset( $this->options['url-selector'] ) ? ($this->options['url-selector'] == $key ? ' checked' : '') : ($key == 'rstr' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('This option dictates which URL parameter will be used to change the language.', RSTR_NAME));
	}

	public function enable_body_class_callback(){
		$inputs = array();

		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="enable-body-class-%1$s"><input type="radio" id="enable-body-class-%1$s" name="%3$s[enable-body-class]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['enable-body-class'] ) ? ($this->options['enable-body-class'] == $key ? ' checked' : '') : ($key == 'no' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('This option adds CSS classes to your body HTML tag. These CSS classes vary depending on the language script.', RSTR_NAME));
	}

	public function force_email_transliteration_callback () {
		$inputs = array();

		foreach(array(
			'yes' => __('Yes', RSTR_NAME),
			'no' => __('No', RSTR_NAME)
		) as $key=>$label)
		{
			$inputs[]=sprintf(
				'<label for="force-email-transliteration-%1$s"><input type="radio" id="force-email-transliteration-%1$s" name="%3$s[force-email-transliteration]" value="%1$s"%4$s> <span>%2$s</span></label>',
				esc_attr($key),
				esc_html($label),
				RSTR_NAME,
				(isset( $this->options['force-email-transliteration'] ) ? ($this->options['force-email-transliteration'] == $key ? ' checked' : '') : ($key == 'no' ? ' checked' : ''))
			);
		}
		printf('%1$s<br><p class="description">%2$s</p>', join(' ', $inputs), __('Enable this feature if you want to force transliteration of email content.', RSTR_NAME));
	}




	/* PRIVATE - List filter mode options in the AJAX and admin */
	private function private___list_filter_mode_options($list, $options=array()) {
		$inputs = array();
		$i = 0;

		if(empty($options)) $options = $this->options;

		$only_woo = false;
		if(RSTR_WOOCOMMERCE && $options['mode'] == 'woocommerce') {
			$only_woo = true;
		}

		foreach($list as $k=>$hook)
		{
			if($only_woo && strpos($hook, 'woo') === false) continue;

			$inputs[$i][]=sprintf(
				'<p><label for="transliteration-filter-%1$s"><input type="checkbox" id="transliteration-filter-%1$s" name="%3$s[transliteration-filter][]" value="%1$s" data-nonce="%4$s"%5$s> <span>%2$s</span></label></p>',
				esc_attr($hook),
				esc_html($hook),
				RSTR_NAME,
				$this->nonce,
				(isset( $options['transliteration-filter']) ? (is_array($options['transliteration-filter']) && in_array($hook, $options['transliteration-filter']) ? ' checked' : '') : '')
			);

			if($i === 2) $i=0; else ++$i;
		}
		?>
		<div class="col"><?php echo isset($inputs[0]) ? join(PHP_EOL, $inputs[0]) : ''; ?></div>
		<div class="col"><?php echo isset($inputs[1]) ? join(PHP_EOL, $inputs[1]) : ''; ?></div>
		<div class="col"><?php echo isset($inputs[2]) ? join(PHP_EOL, $inputs[2]) : ''; ?></div>
		<?php
	}

}
endif;
