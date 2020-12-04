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
	private $active_filters;

    /**
     * Start up
     */
    public function __construct()
    {
		if(current_user_can('administrator'))
		{
			$this->add_action( 'admin_menu', 'add_plugin_page' );
			$this->add_action( 'admin_init', 'page_init' );
			$this->add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );
			$this->add_action( 'plugin_action_links_' . RSTR_BASENAME, 'action_links' );
			$this->add_action( 'plugin_row_meta', 'row_meta_links', 10, 2);

			$this->nonce = esc_attr(wp_create_nonce('rstr-options'));
			
			if($mode_class = $this->mode()) {
				$this->active_filters = array_keys($mode_class::filters());
			}
			
			if($active_plugins = Serbian_Transliteration_Plugins::includes(array(), true)->active_filters())
			{
				$active_plugins = array_keys($active_plugins);
				$this->active_filters = array_merge($this->active_filters, $active_plugins);
			}
			
			if($active_themes = Serbian_Transliteration_Themes::includes(array(), true)->active_filters())
			{
				$active_themes = array_keys($active_themes);
				$this->active_filters = array_merge($this->active_filters, $active_themes);
			}
			
			$this->add_action( 'wp_ajax_rstr_filter_mode_options', 'ajax__rstr_filter_mode_options');
		}
    }
	
	function ajax__rstr_filter_mode_options( ) {
		
		$mode_class = $this->mode(array('mode'=>sanitize_text_field($_POST['mode'])));
		
		if($mode_class !== false && isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'rstr-options') !== false)
		{
		
			$options = get_rstr_option();
			$options['mode'] = sanitize_text_field($_POST['mode']);
			
			$inputs = array();
			$i = 0;
			
			$list = array_keys($mode_class::filters());
			if($active_plugins = Serbian_Transliteration_Plugins::includes($options, true)->active_filters())
			{
				$active_plugins = array_keys($active_plugins);
				$list = array_merge($list, $active_plugins);
			}
			
			if($active_themes = Serbian_Transliteration_Themes::includes($options, true)->active_filters())
			{
				$active_themes = array_keys($active_themes);
				$this->active_filters = array_merge($this->active_filters, $active_themes);
			}
			
			$only_woo = false;
			if(RSTR_WOOCOMMERCE && isset($options['mode']) && $options['mode'] == 'woocommerce') $only_woo = true;

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
		exit;
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
		wp_register_style( RSTR_NAME, RSTR_ASSETS . '/css/serbian-transliteration.css', array('common'), (string)RSTR_VERSION );
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
        $this->options = get_rstr_option();
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
		
		if($this->active_filters) {
			$this->add_settings_field(
				'transliteration-filter', // ID
				__('Transliteration Filters', RSTR_NAME), // Title 
				'transliteration_filter_callback', // Callback
				RSTR_NAME, // Page
				RSTR_NAME . '-global' // Section           
			);
		}
		
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
            'cache-support', // ID
            __('Enable cache support', RSTR_NAME), // Title 
            'cache_support_callback', // Callback
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

		if(isset($new_input[RSTR_NAME]['transliteration-mode']))
		{
			switch($new_input[RSTR_NAME]['transliteration-mode'])
			{
				case 'cyr_to_lat':
					$this->setcookie('lat');
					break;
					
				case 'lat_to_cyr':
					$this->setcookie('cyr');
					break;
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
     * Transliteration filter
     */
    public function transliteration_filter_callback()
    { 
		if(!$this->active_filters) return;
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

		$inputs = array();
		$i = 0;
		
		$list = $this->active_filters;

		$only_woo = false;
		if(RSTR_WOOCOMMERCE && get_rstr_option('mode') == 'woocommerce') $only_woo = true;

		foreach($list as $k=>$hook)
		{
			if($only_woo && strpos($hook, 'woo') === false) continue;
			$inputs[$i][]=sprintf(
				'<p><label for="transliteration-filter-%1$s"><input type="checkbox" id="transliteration-filter-%1$s" name="%3$s[transliteration-filter][]" value="%1$s" data-nonce="%4$s"%5$s> <span>%2$s</span></label></p>',
				esc_attr($hook),
				esc_html($hook),
				RSTR_NAME,
				$this->nonce,
				(isset( $this->options['transliteration-filter']) ? (is_array($this->options['transliteration-filter']) && in_array($hook, $this->options['transliteration-filter']) ? ' checked' : '') : '')
			);
			
			if($i === 2) $i=0; else ++$i;
		}
		?>
		<div class="row" id="rstr-filter-mode-options">
			<div class="col"><?php echo isset($inputs[0]) ? join(PHP_EOL, $inputs[0]) : ''; ?></div>
			<div class="col"><?php echo isset($inputs[1]) ? join(PHP_EOL, $inputs[1]) : ''; ?></div>
			<div class="col"><?php echo isset($inputs[2]) ? join(PHP_EOL, $inputs[2]) : ''; ?></div>
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
}
endif;