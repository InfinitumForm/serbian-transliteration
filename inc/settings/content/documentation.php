<?php if ( !defined('WPINC') ) die();
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
	case 'tags':
		$this->add_action('rstr/settings/tab/content/tags', 'tab_content_available_tags');
		do_action('rstr/settings/tab/content/tags');
        break;
}