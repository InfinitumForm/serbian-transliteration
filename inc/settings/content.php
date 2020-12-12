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
		$this->add_action('rstr/settings/tab', 'nav_tab_transliteration');
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
			case 'transliteration':
				$this->add_action('rstr/settings/tab/content/transliteration', 'tab_content_transliteration');
				break;
			case 'debug':
				$this->add_action('rstr/settings/tab/content/debug', 'tab_content_debug');
				break;
		}
	}
	
	/*
	 * Nav tab settings
	**/
	public function nav_tab_settings(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=settings'); ?>" class="dashicons-before dashicons-admin-settings nav-tab<?php echo is_null($this->tab) || $this->tab == 'settings' ? ' nav-tab-active' : '' ;?>"><span><?php _e('General Settings', RSTR_NAME); ?></span></a>
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
	 * Nav tab transliteration tool
	**/
	public function nav_tab_transliteration(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=transliteration'); ?>" class="dashicons-before dashicons-translation nav-tab<?php echo $this->tab == 'transliteration' ? ' nav-tab-active' : '' ;?>"><span><?php _e('Transliteration Tool', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Tab transliteration tool form
	**/
	public function tab_content_transliteration(){
		wp_enqueue_style( RSTR_NAME );
		wp_enqueue_script( RSTR_NAME );	
		include_once RSTR_INC . '/settings/content/transliteration-tool.php';
	}
	
	/*
	 * Nav tab permalink tools
	**/
	public function nav_tab_permalink_tool(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=permalink_tool'); ?>" class="dashicons-before dashicons-admin-links nav-tab<?php echo $this->tab == 'permalink_tool' ? ' nav-tab-active' : '' ;?>"><span><?php _e('Permalink Tool', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Tab permalink tools content
	**/
	public function tab_content_permalink_tool(){
		include_once RSTR_INC . '/settings/content/permalink-tool.php';
	}
	
	/*
	 * Nav tab Shortcodes
	**/
	public function nav_tab_shortcodes(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=shortcodes'); ?>" class="dashicons-before dashicons-shortcode nav-tab<?php echo $this->tab == 'shortcodes' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-shortcodes"><span><?php _e('Available shortcodes', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Available shortcode section
	**/
	public function tab_content_available_shortcodes(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		include_once RSTR_INC . '/settings/content/shortcodes.php';
	}
	
	/*
	 * Nav tab Functions
	**/
	public function nav_tab_functions(){ ?>
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=functions'); ?>" class="dashicons-before dashicons-code-standards nav-tab<?php echo $this->tab == 'functions' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-functions"><span><?php _e('Available PHP functions', RSTR_NAME); ?></span></a>
	<?php }
	
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
		<a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=debug'); ?>" class="dashicons-before dashicons-sos nav-tab<?php echo $this->tab == 'debug' ? ' nav-tab-active' : '' ;?>" id="rstr-settings-tab-functions"><span><?php _e('Debug', RSTR_NAME); ?></span></a>
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