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
		<a href="<?php echo esc_url( admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=settings') ); ?>" class="dashicons-before dashicons-admin-settings nav-tab<?php echo esc_attr(is_null($this->tab) || $this->tab == 'settings' ? ' nav-tab-active' : '') ;?>" id="rstr-settings-tab-settings"><span><?php esc_html_e('General Settings', RSTR_NAME); ?></span></a>
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
		<a href="<?php echo esc_url( admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=tools&action=transliteration') ); ?>" class="dashicons-before dashicons-admin-generic nav-tab<?php echo esc_attr( $this->tab == 'tools' ? ' nav-tab-active' : '') ;?>" id="rstr-settings-tab-tools"><span><?php esc_html_e('Tools', RSTR_NAME); ?></span></a>
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
	
	/*
	 * Nav tab documentation
	**/
	public function nav_tab_documentation(){ ?>
		<a href="<?php echo esc_url( admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=documentation&action=shortcodes') ); ?>" class="dashicons-before dashicons-media-spreadsheet nav-tab<?php echo esc_attr($this->tab == 'documentation' ? ' nav-tab-active' : '') ;?>" id="rstr-settings-tab-documentation"><span><?php esc_html_e('Documentation', RSTR_NAME); ?></span></a>
	<?php }
	
	/*
	 * Action tab documentation
	**/
	public function nav_tab_documentation_action(){
		$this->admin_action_links(array(
			'shortcodes' => __('Available shortcodes', RSTR_NAME),
			'functions' => __('Available PHP functions', RSTR_NAME),
			'tags' => __('Available Tags', RSTR_NAME)
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
	 * Available tags section
	**/
	public function tab_content_available_tags(){
		wp_enqueue_style( 'highlight');
		wp_enqueue_script('highlight');
		include_once RSTR_INC . '/settings/content/tags.php';
	}
	
	/*
	 * Debug
	**/
	public function nav_tab_debug(){ ?>
		<a href="<?php echo esc_url( admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=debug') ); ?>" class="dashicons-before dashicons-sos nav-tab<?php echo esc_attr($this->tab == 'debug' ? ' nav-tab-active' : ''); ?>" id="rstr-settings-tab-debug"><span><?php esc_html_e('Debug', RSTR_NAME); ?></span></a>
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
		<a href="<?php echo esc_url( admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=credits') ); ?>" class="dashicons-before dashicons-info-outline nav-tab<?php echo esc_attr($this->tab == 'credits' ? ' nav-tab-active' : ''); ?>" id="rstr-settings-tab-credits"><span><?php esc_html_e('Credits & Info', RSTR_NAME); ?></span></a>
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
<div class="tab-content" id="tab-<?php echo esc_attr($this->tab ? sprintf('%s-%s', RSTR_NAME, $this->tab) : RSTR_NAME); ?>">
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
	* Admin action links
	* @verson    1.0.0
	*/
	public function admin_action_links($actions = array()) {
		$active = (isset($_GET['action']) ? $_GET['action'] : '');
		$tab = (isset($_GET['tab']) ? $_GET['tab'] : '');
	?>
<ul class="action-links">
<?php foreach($actions as $action=>$name): ?>
	<li class="action-tab<?php echo ($action==$active ? ' active' : ''); ?>"><a href="<?php echo esc_url( admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=' . $tab . '&action=' . $action) ); ?>" class="action-link<?php echo esc_attr($action==$active ? ' active' : ''); ?>"><?php echo esc_html($name); ?></a></li>
<?php endforeach; ?>
</ul>
<select class="action-links-select" onchange="location = this.value;">
<?php foreach($actions as $action=>$name): ?>
	<option value="<?php echo esc_url( admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=' . $tab . '&action=' . $action) ); ?>"<?php echo esc_attr($action==$active ? ' selected' : ''); ?>><?php echo esc_html($name); ?></option>
<?php endforeach; ?>
</select>
	<?php
	}
	
}
endif;