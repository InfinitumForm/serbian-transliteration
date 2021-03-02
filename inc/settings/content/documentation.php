<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
switch($this->action)
{
    case 'functions':
		$this->add_action('rstr/settings/tab/content/functions', 'tab_content_available_functions');
		do_action('rstr/settings/tab/content/functions');
		break;
    default:
    case 'shortcodes':
		$this->add_action('rstr/settings/tab/content/shortcodes', 'tab_content_available_shortcodes');
		do_action('rstr/settings/tab/content/shortcodes');
        break;
}