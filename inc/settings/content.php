<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Tabs, Content, Tools for the Settings
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @autor             Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Settings_Content')) :
class Serbian_Transliteration_Settings_Content extends Serbian_Transliteration
{
	private $obj;
	private $tab;
	
	function __construct($object)
	{
		$this->obj = $object;
		$this->tab = ((isset($_GET['tab']) && !empty($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : NULL);
		
		$this->add_action('rstr/settings/content', 'nav_tab_wrapper');
		$this->add_action('rstr/settings/content', 'tab_content');
		
		$this->add_action('rstr/settings/tab', 'nav_tab_settings');
		$this->add_action('rstr/settings/tab', 'nav_tab_permalink_tool');
		$this->add_action('rstr/settings/tab', 'nav_tab_shortcodes');
		$this->add_action('rstr/settings/tab', 'nav_tab_functions');
		$this->add_action('rstr/settings/tab', 'nav_tab_debug');
		
		switch($this->tab)
		{
			default:
				$this->add_action('rstr/settings/tab/content', 'tab_content_settings_form');
				break;
			case 'settings':
				$this->add_action('rstr/settings/tab/content/settings', 'tab_content_settings_form');
				break;
			case 'shortcodes':
				$this->add_action('rstr/settings/tab/content/shortcodes', 'tab_content_available_shortcodes');
				break;
			case 'functions':
				$this->add_action('rstr/settings/tab/content/functions', 'tab_content_available_functions');
				break;
			case 'permalink_tool':
				$this->add_action('rstr/settings/tab/content/permalink_tool', 'tab_content_permalink_tool');
				break;
			case 'debug':
				$this->add_action('rstr/settings/tab/content/debug', 'tab_content_debug');
				break;
		}
		
		$this->add_action( 'wp_ajax_rstr_run_permalink_transliteration', 'ajax__run_permalink_transliteration');
	}
	
	/*
	 * Nav tab settings
	**/
	public function nav_tab_settings(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=settings'); ?>" class="nav-tab<?php echo is_null($this->tab) || $this->tab == 'settings' ? ' nav-tab-active' : '' ;?>"><?php _e('General Settings', RSTR_NAME); ?></a>
	<?php }
	
	/*
	 * Nav tab permalink tools
	**/
	public function nav_tab_permalink_tool(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=permalink_tool'); ?>" class="nav-tab<?php echo $this->tab == 'permalink_tool' ? ' nav-tab-active' : '' ;?>"><?php _e('Permalink Tool', RSTR_NAME); ?></a>
	<?php }
	
	/*
	 * Nav tab Shortcodes
	**/
	public function nav_tab_shortcodes(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=shortcodes'); ?>" class="nav-tab<?php echo $this->tab == 'shortcodes' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-shortcodes"><?php _e('Available shortcodes', RSTR_NAME); ?></a>
	<?php }
	
	/*
	 * Nav tab Functions
	**/
	public function nav_tab_functions(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=functions'); ?>" class="nav-tab<?php echo $this->tab == 'functions' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-functions"><?php _e('Available PHP functions', RSTR_NAME); ?></a>
	<?php }
	
	/*
	 * Debug
	**/
	public function nav_tab_debug(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=debug'); ?>" class="nav-tab<?php echo $this->tab == 'debug' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-functions"><?php _e('Debug', RSTR_NAME); ?></a>
	<?php }
	
	/*
	 * Tab permalink tools content
	**/
	public function tab_content_permalink_tool(){ ?>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/shortcodes'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<h1><span><?php _e('Permalink Transliteration Tool', RSTR_NAME); ?></span></h1>
				<?php
					printf('<p>%s</p>', __('This tool can rename all existing Cyrillic permalinks to Latin inside database.', RSTR_NAME));
					printf('<p>%s</p>', __('This option is dangerous and can create unexpected problems. Once you run this script, all permalinks in your database will be modified and this can affect on the SEO causing a 404 error.',
					RSTR_NAME));
					printf('<p>%s</p>', __('Consult your SEO developer before you run this script as you will then need to resubmit the sitemap and make any other additional settings to change the permalinks on the search engines.', RSTR_NAME));
					printf('<p><strong class="text-danger">%s</strong></p>', sprintf(__('You must %s before running this script.', RSTR_NAME), '<a href="https://wordpress.org/support/article/wordpress-backups/" target="_blank">' . __('back up the database', RSTR_NAME) . '</a>'));

					$get_post_types = get_post_types(array(
						'public'   => true
					), 'names', 'and');
					/*
					$post_types = array_map(function($match){
						return '<code><b>' . $match . '</b></code>';
					}, $get_post_types);
					*/
					$post_types_selector = array_map(function($match){
						return sprintf('<label for="tools-transliterate-post-type-%1$s"><input type="checkbox" id="tools-transliterate-post-type-%1$s" value="%1$s" class="tools-transliterate-permalinks-post-types" name="tools-transliterate-permalinks-post-types[]" checked><span>%2$s</span></label>', $match, strtr($match, array('_'=>' ',  '-'=>' ')));
					}, $get_post_types);
					
					printf('<p>%s</p>', sprintf(__('This tool will affect on the following post types: %s', RSTR_NAME), '<br>' . join('&nbsp;&nbsp;&nbsp;&nbsp; ', $post_types_selector)));
				?>
				<br>
				<div id="rstr-progress-bar" style="display:none;">
					<p class="progress-value" style="width:0%" data-value="0"></p>
					<progress max="100" value="0" class="php">
						<!-- Browsers that support HTML5 progress element will ignore the html inside `progress` element. Whereas older browsers will ignore the `progress` element and instead render the html inside it. -->
						<div class="progress-bar">
							<span style="width: 0%">0%</span>
						</div>
					</progress>
					<p class="progress-message"><?php _e('Please wait! Do not close the window or leave the page until this operation is completed!', RSTR_NAME); ?></p>
				</div>
				<p>
					<input type="button" id="<?php echo RSTR_NAME ?>-tools-transliterate-permalinks" class="button button-primary" data-nonce="<?php echo esc_attr(wp_create_nonce('rstr-run-permalink-transliteration')); ?>" value="<?php esc_attr_e('Let\'s do magic', RSTR_NAME); ?>" disabled>
					&nbsp;&nbsp;&nbsp;
					<label for="<?php echo RSTR_NAME ?>-tools-check">
						<input type="checkbox" id="<?php echo RSTR_NAME ?>-tools-check" value="1"> <?php _e('Are you sure you want this?', RSTR_NAME); ?>
					</label>
				</p>
				<blockquote id="rstr-disclaimer" style="display:none;">
					<h3><?php _e('Disclaimer', RSTR_NAME); ?></h3>
					<?php printf('<p>%s</p>', __('This tool is made to work safely but there is always a small chance of some unpredictable problem.', RSTR_NAME)); ?>
					<?php printf('<p><b>%s</b></p>', __('WE DO NOT GUARANTEE THAT THIS TOOL WILL FUNCTION PROPERLY ON YOUR SERVER AND BY USING THIS TOOL YOU ARE RESPONSIBLE FOR RISK AND POSSIBLE PROBLEMS.', RSTR_NAME)); ?>
					<?php printf('<p><b>%s</b></p>', __('BACKUP YOUR DATABASE BEFORE USE IT.', RSTR_NAME)); ?>
				</blockquote>
			</div>
		</div>
		<br class="clear">
	</div>

</div><?php }
	
	/*
	 * Tab settings form
	**/
	public function tab_content_settings_form(){ ?>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<form method="post" action="<?php echo esc_url(admin_url('/options.php')); ?>" id="<?php echo RSTR_NAME; ?>-settings-form">
				<?php
					settings_fields( RSTR_NAME . '-group' );
					settings_fields( RSTR_NAME . '-search' );
					do_settings_sections( RSTR_NAME );
					submit_button();
				?>
				</form>
			</div>
		</div>
		<br class="clear">
	</div>

</div>
	<?php }
	
	/*
	 * Available shortcode section
	**/
	public function tab_content_available_shortcodes(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('.lang-txt').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/shortcodes'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<h1><span><?php _e('Available shortcodes', RSTR_NAME); ?></span></h1>
				<div class="inside">
					<br>
					<h2 style="margin:0;"><?php _e('Cyrillic to Latin', RSTR_NAME); ?>:</h2>
					<p><code class="lang-txt">[<span class="hljs-title">rstr_cyr_to_lat</span>]Ћирилица у латиницу[/<span class="hljs-title">rstr_cyr_to_lat</span>]</code></p>
					<br>
					<h2 style="margin:0;"><?php _e('Latin to Cyrillic', RSTR_NAME); ?>:</h2>
					<p><code class="lang-txt">[<span class="hljs-title">rstr_lat_to_cyr</span>]Latinica u ćirilicu[/<span class="hljs-title">rstr_lat_to_cyr</span>]</code></p>
					<br>
					<h2 style="margin:0;"><?php _e('Add an image depending on the language script', RSTR_NAME); ?>:</h2>
					<?php printf('<p>%s</p>', __('With this shortcode you can manipulate images and display images in Latin or Cyrillic depending on the setup.', RSTR_NAME)); ?>
					<p><code class="lang-txt">[<span class="hljs-title">rstr_img</span>]</code></p>
					<h4><?php _e('Example', RSTR_NAME); ?>:</h4>
					<p><code class="lang-txt">[<span class="hljs-title">rstr_img</span> <span class="hljs-params"><span class="hljs-keyword">lat</span>="<?php echo home_url('/logo_latin.jpg') ?>"</span> <span class="hljs-params"><span class="hljs-keyword">cyr</span>="<?php echo home_url('/logo_cyrillic.jpg') ?>"</span>]</code></p>
					<h3><?php _e('Main shortcode parameters', RSTR_NAME); ?>:</h3>
					<ul>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'lat', __('URL (src) as shown in the Latin language', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'cyr', __('URL (src) as shown in the Cyrillic language', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'default', __('(optional) URL (src) to the default image if Latin and Cyrillic are unavailable', RSTR_NAME)); ?>
					</ul>
					<h3><?php _e('Optional shortcode parameters', RSTR_NAME); ?>:</h3>
					<ul>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'cyr_title', __('(optional) title (alt) description of the image for Cyrillic', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'cyr_caption', __('(optional) caption description of the image for Cyrillic', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'lat_title', __('(optional) title (alt) description of the image for Latin', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'lat_caption', __('(optional) caption description of the image for Latin', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'default_title', __('(optional) title (alt) description of the image if Latin and Cyrillic are unavailable', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'default_caption', __('(optional) caption description of the imag if Latin and Cyrillic are unavailable', RSTR_NAME)); ?>
					</ul>
					<h3><?php _e('Shortcode return', RSTR_NAME); ?>:</h3>
					<?php printf('<p>%s</p>', __('HTML image corresponding to the parameters set in this shortcode.', RSTR_NAME)); ?>
					
					<br>
					<h2 style="margin:0;"><?php _e('Language script menu', RSTR_NAME); ?>:</h2>
					<?php printf('<p>%s</p>', __('This shortcode displays a selector for the transliteration script.', RSTR_NAME)); ?>
					<p><code class="lang-txt">[<span class="hljs-title">rstr_selector</span>]</code></p>
					<h3><?php _e('Optional shortcode parameters', RSTR_NAME); ?>:</h3>
					<ul>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'type', sprintf(__('(string) The type of selector that will be displayed on the site. It can be: "%1$s", "%2$s" or "%3$s"', RSTR_NAME), 'inline', 'select', 'list')); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'separator', sprintf(__('(string) Separator to be used when the selector type is %s. Default: %s', RSTR_NAME), 'inline', ' | ')); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'cyr_caption', __('(string) Text for Cyrillic link. Default: Cyrillic', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'lat_caption', __('(string) Text for Latin link. Default: Latin', RSTR_NAME)); ?>
					</ul>
					<br><br>
					<?php printf('<p><b>%s</b></p>', __('This shortcodes work independently of the plugin settings and can be used anywhere within WordPress pages, posts, taxonomies and widgets (if they support it).', RSTR_NAME)); ?>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>

</div>
	<?php }
	
	/*
	 * Tab Debug
	**/
	public function tab_content_debug(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		include_once RSTR_INC . '/OS.php';
		$activations = get_option( RSTR_NAME . '-activation' );
		$options = get_option( RSTR_NAME );
	?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('.lang-php').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/debug'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<h2><span><?php _e('Debug information', RSTR_NAME); ?></span></h2>
				<table class="table table-sm table-striped w-100">
					<thead><?php do_action('rstr/settings/debug/table/thead'); ?></thead>
					<tbody>
						<?php do_action('rstr/settings/debug/table/tbody/start'); ?>
						<tr>
							<td width="30%" style="width:30%;"><strong><?php _e( 'Plugin ID', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_option(RSTR_NAME . '-ID'); ?></td>
						</tr>
						<tr>
							<td width="30%" style="width:30%;"><strong><?php _e( 'Plugin version', RSTR_NAME ); ?></strong></td>
							<td><?php echo RSTR_VERSION; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress version', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'version' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Last plugin update', RSTR_NAME ); ?></strong></td>
							<td><?php echo (!empty($activations) ? end($activations) : '-'); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'PHP version', RSTR_NAME ); ?></strong></td>
							<td>PHP <?php echo PHP_VERSION; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'PHP version ID', RSTR_NAME ); ?></strong></td>
							<td><?php echo PHP_VERSION_ID; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'PHP architecture', RSTR_NAME ); ?></strong></td>
							<td><?php printf(__('%d bit', RSTR_NAME), (Serbian_Transliteration_OS::is_php64() ? 64 : 32)); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress debug', RSTR_NAME ); ?></strong></td>
							<td><?php echo ( WP_DEBUG ? '<strong><span style="color:#007d1b">' . __( 'On', RSTR_NAME ) . '</span></strong>' : __( 'Off', RSTR_NAME ) ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress multisite', RSTR_NAME ); ?></strong></td>
							<td><?php echo ( RSTR_MULTISITE ? '<strong><span style="color:#007d1b">' . __( 'On', RSTR_NAME ) . '</span></strong>' : __( 'Off', RSTR_NAME ) ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Site title', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'name' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Tagline', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'description' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress address (URL)', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'wpurl' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Admin email', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'admin_email' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Encoding for pages and feeds', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'charset' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Content-Type', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_bloginfo( 'html_type' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Site Language', RSTR_NAME ); ?></strong></td>
							<td><?php echo get_locale(); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Server time', RSTR_NAME ); ?></strong></td>
							<td><?php echo date( 'r' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'WordPress directory path', RSTR_NAME ); ?></strong></td>
							<td><?php echo ABSPATH; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Operting system', RSTR_NAME ); ?></strong></td>
							<td><?php echo Serbian_Transliteration_OS::getOS(); ?> <?php printf(__('%d bit', RSTR_NAME), Serbian_Transliteration_OS::architecture()); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'User agent', RSTR_NAME ); ?></strong></td>
							<td><?php echo Serbian_Transliteration_OS::user_agent(); ?></td>
						</tr>
						<tr>
							<td><strong><?php _e( 'Plugin directory path', RSTR_NAME ); ?></strong></td>
							<td><?php echo RSTR_ROOT; ?></td>
						</tr>
						<?php do_action('rstr/settings/debug/table/tbody/end'); ?>
					</tbody>
					<tfoot><?php do_action('rstr/settings/debug/table/tfoot'); ?></tfoot>
				</table>
			
				<h2><span><?php _e('Plugin settings', RSTR_NAME); ?></span></h2>
				<pre class="lang-php">
["<?php echo RSTR_NAME; ?>"] => <?php var_dump($options); ?>
				</pre>
			</div>
		</div>
		<br class="clear">
	</div>

</div>
	<?php }
	
	/*
	 * Available functions section
	**/
	public function tab_content_available_functions(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		?>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
	document.querySelectorAll('code.lang-php').forEach((block) => {
		hljs.highlightBlock(block);
	});
});
</script>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('rstr/settings/sidebar/tab/functions'); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<h1><span><?php _e('Available PHP Functions', RSTR_NAME); ?></span></h1>
				<div class="inside">
					<br>
					<h3 style="margin:0;">is_cyrillic_text</h3>
					<p><code class="lang-php">function is_cyrillic_text(string $content) : bool</code></p>
					<br>
					<h3 style="margin:0;">is_latin_text</h3>
					<p><code class="lang-php">function is_latin_text(string $content) : bool</code></p>
					<br>
					<h3 style="margin:0;">is_already_cyrillic</h3>
					<?php printf('<p>%s</p>', __('Determines whether the site is already in Cyrillic.', RSTR_NAME)); ?>
					<p><code class="lang-php">function is_already_cyrillic() : bool</code></p>
					<br>
					<h3 style="margin:0;">is_cyrillic</h3>
					<p><code class="lang-php">function is_cyrillic() : bool</code></p>
					<br>
					<h3 style="margin:0;">is_latin</h3>
					<p><code class="lang-php">function is_latin() : bool</code></p>
					<br>
					<h3 style="margin:0;">is_serbian</h3>
					<p><code class="lang-php">function is_serbian() : bool</code></p>
					<br>
					<h3 style="margin:0;">is_russian</h3>
					<p><code class="lang-php">function is_russian() : bool</code></p>
					<br>
					<h3 style="margin:0;">is_belarusian</h3>
					<p><code class="lang-php">function is_belarusian() : bool</code></p>
					<br>
					<h3 style="margin:0;">is_bulgarian</h3>
					<p><code class="lang-php">function is_bulgarian() : bool</code></p>
					<br>
					<h3 style="margin:0;">is_macedonian</h3>
					<p><code class="lang-php">function is_macedonian() : bool</code></p>
					<br>
					<h3 style="margin:0;">transliterate</h3>
					<?php printf('<p>%s</p>', __('Transliteration of some text or content into the desired script.', RSTR_NAME)); ?>
					<p><code class="lang-php">function transliterate(string $content, string $type='cyr_to_lat', bool $fix_html = true) : string</code></p>
					<?php printf('<p>%s</p>', __('The <b><i>$type</i></b> parameter has two values: <code>cyr_to_lat</code> (Cyrillic to Latin) and <code>lat_to_cyr</code> (Latin to Cyrillic)', RSTR_NAME)); ?>
					<br>
					<h3 style="margin:0;">get_script</h3>
					<?php printf('<p>%s</p>', __('Get active script.', RSTR_NAME)); ?>
					<p><code class="lang-php">function get_script() : string</code></p>
					<br>
					<h3 style="margin:0;">script_selector</h3>
					<?php printf('<p>%s</p>', __('This function displays a selector for the transliteration script.', RSTR_NAME)); ?>
					<p><code class="lang-php">function script_selector(array $args) : string|echo|array|object</code></p>
					<h4><?php _e('Parameters', RSTR_NAME); ?></h4>
					<?php printf('<p><b><code>$args</code></b> (array) - %1$s</p>', __('This attribute contains an associative set of parameters for this function:', RSTR_NAME)); ?>
					<ul>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'display_type', sprintf(__('(string) The type of selector that will be displayed on the site. It can be: "%1$s", "%2$s", "%3$s", "%4$s" or "%5$s". Default: "%1$s"', RSTR_NAME), 'inline', 'select', 'list', 'array', 'object')); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'echo', __('(bool) determines whether it will be displayed through an echo or as a string. Default: false', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'separator', sprintf(__('(string) Separator to be used when the selector type is %s. Default: %s', RSTR_NAME), 'inline', ' | ')); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'cyr_caption', __('(string) Text for Cyrillic link. Default: Cyrillic', RSTR_NAME)); ?>
						<?php printf('<li><code>%1$s</code> - %2$s</li>', 'lat_caption', __('(string) Text for Latin link. Default: Latin', RSTR_NAME)); ?>
					</ul>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>

</div>
	<?php }
	
	/*
	 * Tab content container
	**/
	public function tab_content(){ ?>
<div class="tab-content" id="tab-<?php $this->tab ? printf('%s-%s', RSTR_NAME, $this->tab) : RSTR_NAME; ?>">
	<?php 
		do_action('rstr/settings/tab/content/before');
		if(is_null($this->tab)) {
			do_action('rstr/settings/tab/content');
		} else {
			do_action('rstr/settings/tab/content/' . $this->tab, $this->tab);
		}
		do_action('rstr/settings/tab/content/after');
	?>
</div>
	<?php }
	
	/*
	 * Tab navbar
	**/
	public function nav_tab_wrapper(){ ?>
<nav class="nav-tab-wrapper">
	<?php do_action('rstr/settings/tab');?>
</nav>
	<?php }
	
	/*
	 * AJAX update permalinks cyr to lat
	**/
	public function ajax__run_permalink_transliteration () {
		global $wpdb;
		
		$data = array(
			'error' => true,
			'done'   => false,
			'message' => __('There was a communication problem. Please refresh the page and try again. If this does not solve the problem, contact the author of the plugin.', RSTR_NAME),
			'loading' => false
		);
		
		if(isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'rstr-run-permalink-transliteration') !== false)
		{
			// Posts per page
			$posts_pre_page = apply_filters('rstr/permalink-tool/transliteration/offset', 20);
			
			// Set post type
			if(isset($_REQUEST['post_type']) && !empty($_REQUEST['post_type'])) {
				if(is_array($_REQUEST['post_type'])) {
					$post_type = join(',', array_map(function($val){
						return sanitize_text_field($val);
					}, $_REQUEST['post_type']));
				} else {
					$post_type = sanitize_text_field($_REQUEST['post_type']);
				}
				$post_type_query = "FIND_IN_SET(`post_type`, '{$post_type}')";
			} else {
				$post_type = NULL;
				$post_type_query = 0;
			}
			
			// Get maximum number of the posts
			if(isset($_POST['total'])){
				$total = absint($_POST['total']);
			} else {
				$total = absint($wpdb->get_var("SELECT COUNT(1) FROM `{$wpdb->posts}` WHERE {$post_type_query} AND `post_type` NOT LIKE 'revision' AND TRIM(IFNULL(`post_name`,'')) <> '' AND `post_status` NOT LIKE 'trash'"));
			}
			
			// Get updated
			$updated = (isset($_POST['updated']) ? absint($_POST['updated']) : 0);
			
			// Get current page
			$paged = (isset($_POST['paged']) ? absint($_POST['paged'])+1 : 1);
			
			// Calculate offset
			$pages = ceil($total / $posts_pre_page);
				if($pages < 1) $pages = 1;
			
			// Percentage
			$percentage = round((($paged/$pages)*100),2);
			if($percentage > 100) $percentage = 100;
			if($percentage < 0) $percentage = 0;
			
			// Let's do the transliteration
			$return = array();
			if($total) {
				$offset = ($paged-1);
				if($offset<0) $offset = 0;
				$offset = ($posts_pre_page*$offset);
				
				$limit = $wpdb->prepare('LIMIT %d, %d', $offset, $posts_pre_page);
				$get_results = $wpdb->get_results("SELECT `ID`, `post_name` FROM `{$wpdb->posts}` WHERE {$post_type_query} AND TRIM(IFNULL(`post_name`,'')) <> '' AND `post_type` NOT LIKE 'revision' AND `post_status` NOT LIKE 'trash' ORDER BY `ID` DESC {$limit}");
				
				if($get_results)
				{
					// Fix  problematic
					$get_results = array_map(function($match) use (&$updated, &$wpdb, &$return){
						$match->post_name = $this->decode( $match->post_name );
						$match->post_name = $this->cyr_to_lat_sanitize( $match->post_name );
						if($wpdb->update(
							$wpdb->posts,
							array(
								'post_name' => $this->cyr_to_lat_sanitize($match->post_name),
							),
							array(
								'ID' => $match->ID
							), array(
								'%s'
							), array(
								'%d'
							)
						)) {
							++$updated;
							$return[]=$match;
						}
						return $match;
					}, $get_results);
				}
			}
			
			if($paged<$pages)
			{
				$data = array(
					'error'   => false,
					'done'   => false,
					'message' => NULL,
					'posts_pre_page'   => $posts_pre_page,
					'paged'    => $paged,
					'total'   => $total,
					'pages'   => $pages,
					'loading' => true,
					'percentage' => $percentage,
					'updated' => $updated,
					'nonce' => $_REQUEST['nonce'],
					'action' => $_REQUEST['action'],
					'post_type' => $post_type
				);
			}
			else
			{
				$data = array(
					'error'   => false,
					'done'   => true,
					'message' => NULL,
					'loading' => true,
					'percentage' => $percentage,
					'return' => $return,
					'updated' => $updated,
					'nonce' => $_REQUEST['nonce'],
					'action' => $_REQUEST['action'],
					'post_type' => $post_type
				);
			}
		}
		
		header('Content-Type: application/json');
		exit(json_encode($data));
	}
}
endif;