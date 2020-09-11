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
	private static $_instance = null;
	private $obj;
	private $tab;
	
	function __construct($object)
	{
		$this->obj = $object;
		$this->tab = ((isset($_GET['tab']) && !empty($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : NULL);
		
		$this->add_action('serbian-transliteration/settings/content', 'nav_tab_wrapper');
		$this->add_action('serbian-transliteration/settings/content', 'tab_content');
		
		$this->add_action('serbian-transliteration/settings/tab', 'nav_tab_settings');
		$this->add_action('serbian-transliteration/settings/tab', 'nav_tab_permalink_tool');
		$this->add_action('serbian-transliteration/settings/tab', 'nav_tab_shortcodes');
		
		if(is_null($this->tab))
		{
			$this->add_action('serbian-transliteration/settings/tab/content', 'settings_form');
		}
		else
		{
			$this->add_action('serbian-transliteration/settings/tab/content/settings', 'settings_form');
			$this->add_action('serbian-transliteration/settings/tab/content/shortcodes', 'available_shortcodes');
			
			$this->add_action('serbian-transliteration/settings/tab/content/permalink_tool', 'tab_content_permalink_tool');
		}
		
		$this->add_action( 'wp_ajax_rstr_run_permalink_transliteration', 'ajax__run_permalink_transliteration' );
	}
	
	/*
	 * Nav tab settings
	**/
	public function nav_tab_settings(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=settings'); ?>" class="nav-tab<?php echo is_null($this->tab) || $this->tab == 'settings' ? ' nav-tab-active' : '' ;?>"><?php _e('General Settings', RSTR_NAME); ?></a>
	<? }
	
	/*
	 * Nav tab permalink tools
	**/
	public function nav_tab_permalink_tool(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=permalink_tool'); ?>" class="nav-tab<?php echo $this->tab == 'permalink_tool' ? ' nav-tab-active' : '' ;?>"><?php _e('Permalink Tool', RSTR_NAME); ?></a>
	<? }
	
	/*
	 * Nav tab Shortcodes
	**/
	public function nav_tab_shortcodes(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=shortcodes'); ?>" class="nav-tab<?php echo $this->tab == 'shortcodes' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-shortcodes"><?php _e('Available shortcodes', RSTR_NAME); ?></a>
	<? }
	
	/*
	 * Tab permalink tools content
	**/
	public function tab_content_permalink_tool(){ ?>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('serbian-transliteration/settings/sidebar/tab/shortcodes', $this); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<h1><span><?php _e('Permalink Transliteration Tool', RSTR_NAME); ?></span></h1>
				<?
					printf('<p>%s</p>', __('This tool can rename all existing Cyrillic permalinks to Latin inside database.', RSTR_NAME));
					printf('<p>%s</p>', __('This option is dangerous and can create unexpected problems. Once you run this script, all permalinks in your database will be modified and this can affect on the SEO causing a 404 error.',
					RSTR_NAME));
					printf('<p>%s</p>', __('Consult your SEO developer before you run this script as you will then need to resubmit the sitemap and make any other additional settings to change the permalinks on the search engines.', RSTR_NAME));
					printf('<p><strong class="text-danger">%s</strong></p>', sprintf(__('You must %s before running this script.', RSTR_NAME), '<a href="https://wordpress.org/support/article/wordpress-backups/" target="_blank">' . __('back up the database', RSTR_NAME) . '</a>'));

					$post_types = get_post_types(array(
						'public'   => true
					), 'names', 'and');
					
					$post_types = array_map(function($match){
						return '<code><b>' . $match . '</b></code>';
					}, $post_types);
					
					printf('<p>%s</p>', sprintf(__('This tool will affect on the following post types: %s', RSTR_NAME), join(' ', $post_types)));
				?>
				<br>
				<div id="rstr-progress-bar" style="display:none;">
					<p class="progress-value" style="width:33%" data-value="33"></p>
					<progress max="100" value="33" class="php">
						<!-- Browsers that support HTML5 progress element will ignore the html inside `progress` element. Whereas older browsers will ignore the `progress` element and instead render the html inside it. -->
						<div class="progress-bar">
							<span style="width: 33%">33%</span>
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
	public function settings_form(){ ?>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('serbian-transliteration/settings/sidebar', $this); ?>
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
	public function available_shortcodes(){ ?>
<div class="rstr-tab-wrapper">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar" id="<?php echo RSTR_NAME; ?>-settings-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_action('serbian-transliteration/settings/sidebar/tab/shortcodes', $this); ?>
			</div>
		</div>
	 
		<div id="post-body">
			<div id="post-body-content">
				<h3 class="hndle" style="margin-bottom:0;padding-bottom:0;"><span><?php _e('Available shortcodes', RSTR_NAME); ?></span></h3><hr>
				<div class="inside">
					<h4 style="margin:0;"><?php _e('Cyrillic to Latin', RSTR_NAME); ?>:</h4>
					<code>[rstr_cyr_to_lat]Ћирилица у латиницу[/rstr_cyr_to_lat]</code>
					<br><br>
					<h4 style="margin:0;"><?php _e('Latin to Cyrillic', RSTR_NAME); ?>:</h4>
					<code>[rstr_lat_to_cyr]Latinica u ćirilicu[/rstr_lat_to_cyr]</code>
					<?php printf('<p>%s</p>', __('This shortcodes work independently of the plugin settings and can be used anywhere within WordPress pages, posts, taxonomies and widgets (if they support it).', RSTR_NAME)); ?>
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
		do_action('serbian-transliteration/settings/tab/content/before', $this);
		if(is_null($this->tab)) {
			do_action('serbian-transliteration/settings/tab/content', $this);
		} else {
			do_action('serbian-transliteration/settings/tab/content/' . $this->tab, $this->tab);
		}
		do_action('serbian-transliteration/settings/tab/content/after', $this);
	?>
</div>
	<?php }
	
	/*
	 * Tab navbar
	**/
	public function nav_tab_wrapper(){ ?>
<nav class="nav-tab-wrapper">
	<?php do_action('serbian-transliteration/settings/tab', $this);?>
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
			$posts_pre_page = apply_filters('serbian-transliteration/permalink-tool/transliteration/offset', 20);
			
			// Get maximum number of the posts
			if(isset($_POST['total'])){
				$total = absint($_POST['total']);
			} else {
				$total = absint($wpdb->get_var("SELECT COUNT(1) FROM `{$wpdb->posts}` WHERE TRIM(IFNULL(`post_name`,'')) <> '' AND `post_type` NOT LIKE 'revision' AND `post_status` NOT LIKE 'trash'"));
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
				$get_results = $wpdb->get_results("SELECT `ID`, `post_name` FROM `{$wpdb->posts}` WHERE TRIM(IFNULL(`post_name`,'')) <> '' AND `post_type` NOT LIKE 'revision' AND `post_status` NOT LIKE 'trash' ORDER BY `ID` DESC {$limit}");
				
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
					'action' => $_REQUEST['action']
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
					'action' => $_REQUEST['action']
				);
			}
		}
		
		header('Content-Type: application/json');
		exit(json_encode($data));
	}
	
	/*
	 * INSTANCE
	**/
	public static function instance($object)
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self($object);
		}
		return self::$_instance;
	}
}
endif;