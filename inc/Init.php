<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Init class
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 */
if(!class_exists('Serbian_Transliteration_Init', false) && class_exists('Serbian_Transliteration', false)) :
final class Serbian_Transliteration_Init extends Serbian_Transliteration {

	/**
	 * Get singleton instance of global class
	 * @since     1.0.0
	 * @version   1.0.0
	 */
	private static function get_instance()
	{
		$class = self::class;
		$instance = Serbian_Transliteration_Cache::get($class);
		if ( !$instance ) {
			$instance = Serbian_Transliteration_Cache::set($class, new self());
		}
		return $instance;
	}

	/**
	 * Get singleton instance of global class
	 * @since     1.0.9
	 * @version   1.0.0
	 */
	public function remove_menu_page() {
		global $submenu;

		if(isset($submenu['upload.php']) && is_array($submenu['upload.php']))
		{
			foreach($submenu['upload.php'] as $parent=>$locations){
				if(is_array($locations))
				{
					foreach($locations as $i=>$value)
					{
						if(strpos($value, 'rstr-script') !== false) unset($submenu['upload.php'][$parent]);
					}
				}
			}
		}
	}


	public function admin_head(){ ?>
<style>#rstr-script-adder{display:none !important;}</style>
	<?php }

	public function wp_head(){ }

	/*
	 * Run all dependency in the background
	 */
	public static function run_dependency(){		
		$inst = self::get_instance();

		/* Transliterate wp-admin
		====================================*/
		if(is_admin())
		{
			// Load options
			$options = get_rstr_option();
			/* Admin transliterations
			=========================================*/
			if($admin_mode_class = Serbian_Transliteration_Utilities::mode(['mode' => 'admin'])) {
				new $admin_mode_class($options);
			}
		}

		/* Do special filtering
		====================================*/
		add_filter('rstr/transliteration/exclude/filters', function($filters, $options) {
			
			$transliteration_filter = get_rstr_option('transliteration-filter');
			
			if(is_array($transliteration_filter)) {

				$only_woo = false;
				if(RSTR_WOOCOMMERCE && get_rstr_option('mode') == 'woocommerce') $only_woo = true;

				foreach($transliteration_filter as $filter){

					if($only_woo && strpos($filter, 'woo') === false){
						unset($filters[$filter]);
					}

					if( isset($filters[$filter]) ) {
						unset($filters[$filter]);

						if($filter == 'the_excerpt' && isset($filters['get_the_excerpt'])) {
							unset($filters['get_the_excerpt']);
						}
					}
				}
			}
			return $filters;
		}, 10, 2);

		/* Load menu
		====================================*/
		if(!class_exists('Serbian_Transliteration_Menu', false) && file_exists(RSTR_INC . '/Menu.php')) {
			include_once RSTR_INC . '/Menu.php';
		}
		if(class_exists('Serbian_Transliteration_Menu', false)){
			new Serbian_Transliteration_Menu();
		}

		/* Load plugins support
		====================================*/
		Serbian_Transliteration_Plugins::includes();
		/* Load themes support
		====================================*/
		Serbian_Transliteration_Themes::includes();
		/* Load SEO support
		====================================*/
		Serbian_Transliteration_SEO::init();
		/* Load Notice
		====================================*/
		Serbian_Transliteration_Notice::init();
	}

	/*
	 * Run plugin on the frontend
	 */
	public static function run () {
		// Load instance
		$inst = self::get_instance();

		add_action('wp_head', array($inst, 'wp_head'));

		// Register taxonomy
		Serbian_Transliteration_Utilities::attachment_taxonomies();

		if( !is_admin() )
		{
			Serbian_Transliteration_Utilities::set_current_script();
		}
		else
		{
			// Remove admin menu pages
			add_action('admin_menu', array($inst, 'remove_menu_page'));
			// Add soem scripts
			add_action('admin_head', array($inst, 'admin_head'));
			// Load settings page
			if(!class_exists('Serbian_Transliteration_Settings', false) && file_exists(RSTR_INC . '/Settings.php')) {
				include_once RSTR_INC . '/Settings.php';
			}
			if(class_exists('Serbian_Transliteration_Settings', false)){
				$Serbian_Transliteration_Settings = new Serbian_Transliteration_Settings();
				new Serbian_Transliteration_Settings_Sidebar( $Serbian_Transliteration_Settings );
				new Serbian_Transliteration_Settings_Content( $Serbian_Transliteration_Settings );
			}
		}

		// Load shortcodes
		if(!class_exists('Serbian_Transliteration_Shortcodes', false) && file_exists(RSTR_INC . '/Shortcodes.php')) {
			include_once RSTR_INC . '/Shortcodes.php';
		}
		if(class_exists('Serbian_Transliteration_Shortcodes', false)){
			new Serbian_Transliteration_Shortcodes();
		}

		// Initialize plugin mode
		if(in_array( get_rstr_option('mode'), array_keys(Serbian_Transliteration_Utilities::plugin_mode()), true ) !== false)
		{
			if(get_rstr_option('transliteration-mode') != 'none')
			{
				// Include mode class
				if($mode_class = Serbian_Transliteration_Utilities::mode(get_rstr_option())) {
					if(method_exists($mode_class,'run')) {
						$mode_class::run(get_rstr_option());
					} else {
						throw new Exception(sprintf('The static method "$1%s::$2%s" does not exist or is not correctly defined on the line %3%d', $mode_class, 'run', (__LINE__-2)));
					}
				}
			}

			/* Media upload transliteration
			=========================================*/
			if(get_rstr_option('media-transliteration', 'yes') == 'yes'){
				$inst->add_filter('wp_handle_upload_prefilter', 'upload_prefilter', (PHP_INT_MAX-1), 1);
				$inst->add_filter( 'sanitize_file_name', 'sanitize_file_name', (PHP_INT_MAX-1) );
				$inst->add_filter( 'wp_unique_filename', 'sanitize_file_name', (PHP_INT_MAX-1) );
			}

			/* Permalink transliteration
			=========================================*/
			$permalink_transliteration = (get_rstr_option('permalink-transliteration', 'yes') == 'yes');
			$ser_cyr_to_lat_slug = ($permalink_transliteration && $inst->get_locale() == 'sr_RS' && get_option('ser_cyr_to_lat_slug'));
			if($ser_cyr_to_lat_slug) $permalink_transliteration = false;

			if($permalink_transliteration){
				$inst->add_filter('sanitize_title', 'force_permalink_to_latin', (PHP_INT_MAX-1), 1);
				$inst->add_filter('the_permalink', 'force_permalink_to_latin', (PHP_INT_MAX-1), 1);
				$inst->add_filter('wp_unique_post_slug', 'force_permalink_to_latin', (PHP_INT_MAX-1), 1);
				$inst->add_filter('permalink_manager_filter_default_post_uri', 'force_permalink_to_latin', (PHP_INT_MAX-1), 1);
				$inst->add_filter('permalink_manager_filter_default_term_uri', 'force_permalink_to_latin', (PHP_INT_MAX-1), 1);
				$inst->add_filter('wp_insert_post_data', 'force_permalink_to_latin_on_save', (PHP_INT_MAX-1), 2);
			}

			/* WordPress search transliteration
			=========================================*/
			if(get_rstr_option('enable-search', 'no') == 'yes')
			{
				if(!class_exists('Serbian_Transliteration_Search', false) && file_exists(RSTR_INC . '/Search.php')) {
					include_once RSTR_INC . '/Search.php';
				}
				if(class_exists('Serbian_Transliteration_Search', false)){
					new Serbian_Transliteration_Search();
				}
			}

			/* WordPress exlude words
			=========================================*/
			$exclude_latin_words = get_rstr_option('exclude-latin-words', '');
			if(!empty($exclude_latin_words))
			{
				add_filter('rstr/init/exclude/cyr', function($list) use (&$exclude_latin_words){
					$array = array();
					if($split = preg_split('/[\n|]/', $exclude_latin_words))
					{
						$split = array_map('trim',$split);
						$split = array_filter($split);
						if(!empty($split) && is_array($split))
						{
							$array = $split;
						}
					}
					return array_merge($list, $array);
				});
			}

			$exclude_cyrillic_words = get_rstr_option('exclude-cyrillic-words', '');
			if(!empty($exclude_cyrillic_words))
			{
				add_filter('rstr/init/exclude/lat', function($list) use (&$exclude_cyrillic_words){
					$array = array();
					if($split = preg_split('/[\n|]/', $exclude_cyrillic_words))
					{
						$split = array_map('trim',$split);
						$split = array_filter($split);
						if(!empty($split) && is_array($split))
						{
							$array = $split;
						}
					}
					return array_merge($list, $array);
				});
			}

			/* Allows to create users with usernames containing Cyrillic characters
			=========================================*/
			if(get_rstr_option('allow-cyrillic-usernames', 'no') == 'yes')
			{
				add_filter('sanitize_user', function ($username, $raw_username, $strict) {
					$username = wp_strip_all_tags( $raw_username ?? '' );
					$username = remove_accents( $username );

					// Kill octets
					$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
					$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

					// If strict, reduce to ASCII and Cyrillic characters for max portability.
					if ( $strict ){
						$username = preg_replace( '|[^a-zа-я0-9 _.\-@]|iu', '', $username );
					}
					$username = trim( $username );

					// Consolidate contiguous whitespace
					$username = preg_replace( '|\s+|', ' ', $username );

					return $username;
				}, 10, 3);
			}

			/* Add Body CSS class
			=========================================*/
			if(get_rstr_option('enable-body-class', 'no') == 'yes')
			{
				add_filter('body_class', function ($classes){
					if(function_exists('get_script')){
						$script = get_script();
					} else {
						$script = rstr_get_script();
					}
					//body class based on the current script - cyr, lat
					$classes[] = 'rstr-' . $script;
					$classes[] = 'transliteration-' . $script;
					$classes[] = $script;
					return $classes;
				});
			}

			/* Force e-mail transliteration
			=========================================*/
			if(get_rstr_option('force-email-transliteration', 'no') == 'yes')
			{
				add_filter('wp_mail', function ($atts){
					// Get class
					$inst = Serbian_Transliteration::__instance();
					// Fix content
					if(isset($atts['message'])) {
						$atts['message'] = $inst->cyr_to_lat($atts['message']);
					}
					// Fix subject
					if(isset($atts['subject'])) {
						$atts['subject'] = $inst->cyr_to_lat($atts['subject']);
					}
					// Return values
					return $atts;
				}, 10, 1);
			}
		}

		/**
		 * Add a new item under the (Home icon) site name in the toolbar on the backend.
		 */
		add_action( 'wp_before_admin_bar_render', function(){
			if(current_user_can('administrator'))
			{
				global $wp_admin_bar;

				$wp_admin_bar->add_menu( array(
					'parent' => 'site-name',
					'id' => 'serbian_transliteration',
					'title' => __('Transliteration', 'serbian-transliteration'),
					'href' => admin_url( '/options-general.php?page=serbian-transliteration' ),
				));
			}
		});

		/**
		 * Fix plugins tags
		 */
		add_action('wp_loaded', function () {
			ob_start(function($buffer){
//				if( Serbian_Transliteration_Utilities::is_cyr($buffer) ) {
					// Fix internal tags
					$cyr_to_lat = Serbian_Transliteration::__instance()->lat_to_cyr('cyr_to_lat', false);
					$lat_to_cyr = Serbian_Transliteration::__instance()->lat_to_cyr('lat_to_cyr', false);
					$rstr_skip = Serbian_Transliteration::__instance()->lat_to_cyr('rstr_skip', false);

					$buffer = strtr($buffer, array(
						'{'.$cyr_to_lat.'}' => '{cyr_to_lat}',
						'{/'.$cyr_to_lat.'}' => '{/cyr_to_lat}',
						'{'.$lat_to_cyr.'}' => '{lat_to_cyr}',
						'{/'.$lat_to_cyr.'}' => '{/lat_to_cyr}',
						'{'.$rstr_skip.'}' => '{rstr_skip}',
						'{/'.$rstr_skip.'}' => '{/rstr_skip}',
						'['.$cyr_to_lat.']' => '[cyr_to_lat]',
						'[/'.$cyr_to_lat.']' => '[/cyr_to_lat]',
						'['.$lat_to_cyr.']' => '[lat_to_cyr]',
						'[/'.$lat_to_cyr.']' => '[/lat_to_cyr]',
						'['.$rstr_skip.']' => '[rstr_skip]',
						'[/'.$rstr_skip.']' => '[/rstr_skip]'
					));
//				}

				// Fix emails
				if(Serbian_Transliteration_Utilities::get_current_script() == 'lat_to_cyr' && !empty($buffer) && is_string($buffer)) {
					/*$buffer = preg_replace_callback('/([a-z0-9\p{Cyrillic}_\-\.]+@[a-z0-9\p{Cyrillic}_\-\.]+\.[wqyx0-9\p{Cyrillic}_\-\.]+)/iu', function ($m) {
						return Serbian_Transliteration::__instance()->cyr_to_lat($m[1]);
					}, $buffer);*/
				}
				
				// Force AJAX transliteration
				if(get_rstr_option('force-ajax-calls', 'no') == 'yes'){
					if(wp_doing_ajax() && !Serbian_Transliteration_Utilities::skip_transliteration()) {
						if(isset($_REQUEST['action']) && (
							in_array(
								$_REQUEST['action'],
								array(
									'find_posts',
									'heartbeat',
									'query-attachments',
									'wp_block'
								)
							) !== false
							|| preg_match('/^((ct_|oxy_)(.*?))$/i', ($_REQUEST['action'] ?? ''))
							|| preg_match('/^(elementor_(.*?))$/i', ($_REQUEST['action'] ?? ''))
						)) {} else {
							$buffer = Serbian_Transliteration::__instance()->cyr_to_lat($buffer);
						}
					}
				}

				return $buffer;
			}, 0, PHP_OUTPUT_HANDLER_REMOVABLE);
		},PHP_INT_MAX);

		add_action('shutdown', function () {
			if (ob_get_level()) {
				ob_end_flush();
			}
		},0);

	}

}
endif;
