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
	private $action;
	
	function __construct($object)
	{
		$this->obj = $object;
		$this->tab = ((isset($_GET['tab']) && !empty($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : NULL);
		$this->action = ((isset($_GET['action']) && !empty($_GET['action'])) ? sanitize_text_field($_GET['action']) : NULL);
		
		$this->add_action('rstr/settings/content', 'nav_tab_wrapper');
		$this->add_action('rstr/settings/content', 'tab_content');
		
		$this->add_action('rstr/settings/tab', 'nav_tab_settings');
		$this->add_action('rstr/settings/tab', 'nav_tab_tools');
		
		$this->add_action('rstr/settings/tab', 'nav_tab_documentation');
		$this->add_action('rstr/settings/tab', 'nav_tab_debug');
		$this->add_action('rstr/settings/tab', 'nav_tab_credits');
		
		$this->add_action('rstr/settings/tab/content/tools/action', 'nav_tab_tools_action');
		$this->add_action('rstr/settings/tab/content/tools/documentation', 'nav_tab_documentation_action');
		
		switch($this->tab)
		{
			default:
				$this->add_action('rstr/settings/tab/content', 'tab_content_settings_form');
				break;
			case 'settings':
				$this->add_action('rstr/settings/tab/content/settings', 'tab_content_settings_form');
				break;
			case 'documentation':
				$this->add_action('rstr/settings/tab/content/documentation', 'tab_content_documentation');
				break;
			case 'tools':
				$this->add_action('rstr/settings/tab/content/tools', 'tab_content_tools');
				break;
			case 'debug':
				$this->add_action('rstr/settings/tab/content/debug', 'tab_content_debug');
				break;
			case 'credits':
				$this->add_action('rstr/settings/tab/content/credits', 'tab_content_credits');
				break;
		}
	}
	
	/*
	 * Nav tab settings
	**/
	public function nav_tab_settings(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=settings'); ?>" class="dashicons-before dashicons-admin-settings nav-tab<?php echo is_null($this->tab) || $this->tab == 'settings' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-settings"><span><?php _e('General Settings', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Tab settings form
	**/
	public function tab_content_settings_form(){
		wp_enqueue_style( RSTR_NAME );
		wp_enqueue_script( RSTR_NAME );	
		include_once RSTR_INC . '/settings/content/global-settings.php';
	}
	
	/*
	 * Nav tab tools
	**/
	public function nav_tab_tools(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=tools&action=transliteration'); ?>" class="dashicons-before dashicons-admin-generic nav-tab<?php echo $this->tab == 'tools' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-tools"><span><?php _e('Tools', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Tab tools content
	**/
	public function tab_content_tools(){
		include_once RSTR_INC . '/settings/content/tools.php';
	}
	
	/*
	 * Tab action links
	**/
	public function nav_tab_tools_action(){
		$this->admin_action_links(array(
			'transliteration' => __('Transliteration Tool', RSTR_NAME),
			'permalink_tool' => __('Permalink Tool', RSTR_NAME)
		));
	}
	
	/*
	 * Tab transliteration tool form
	**/
	public function tab_content_transliteration(){
		wp_enqueue_style( RSTR_NAME );
		wp_enqueue_script( RSTR_NAME );	
		include_once RSTR_INC . '/settings/content/transliteration-tool.php';
	}
	
	/*
	 * Tab permalink tools content
	**/
	public function tab_content_permalink_tool(){
		include_once RSTR_INC . '/settings/content/permalink-tool.php';
	}

					/*
	 * Documentation section
	**/
	public function tab_content_documentation(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		include_once RSTR_INC . '/settings/content/documentation.php';
	}
					
<<<<<<< .mine
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
	
=======

































>>>>>>> .theirs
	/*
	 * Nav tab documentation
	**/
	public function nav_tab_documentation(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=documentation&action=shortcodes'); ?>" class="dashicons-before dashicons-media-spreadsheet nav-tab<?php echo $this->tab == 'documentation' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-documentation"><span><?php _e('Documentation', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Action tab documentation
	**/
	public function nav_tab_documentation_action(){
		$this->admin_action_links(array(
			'shortcodes' => __('Available shortcodes', RSTR_NAME),
			'functions' => __('Available PHP functions', RSTR_NAME)
		));
	}
	
	/*
	 * Available shortcode section
	**/
	public function tab_content_available_shortcodes(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		include_once RSTR_INC . '/settings/content/shortcodes.php';
	}

	/*
	 * Available functions section
	**/
	public function tab_content_available_functions(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		include_once RSTR_INC . '/settings/content/php-functions.php';
	}
	 
	/*
	 * Debug
	**/
	public function nav_tab_debug(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=debug'); ?>" class="dashicons-before dashicons-sos nav-tab<?php echo $this->tab == 'debug' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-debug"><span><?php _e('Debug', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Tab Debug
	**/
	public function tab_content_debug(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		include_once RSTR_INC . '/settings/content/debug.php';
	}

	/*
	 * Credits
	**/
	public function nav_tab_credits(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=credits'); ?>" class="dashicons-before dashicons-info-outline nav-tab<?php echo $this->tab == 'credits' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-credits"><span><?php _e('Credits & Info', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Tab Credits
	**/
	public function tab_content_credits(){
		include_once RSTR_INC . '/settings/content/credits.php';
	}

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
	
				}
endif;