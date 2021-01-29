<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
switch($this->action)
{
    case 'permalink_tool':
        $this->add_action('rstr/settings/tab/content/permalink_tool', 'tab_content_permalink_tool');
		do_action('rstr/settings/tab/content/permalink_tool');
        break;
    default:
    case 'transliteration':
        $this->add_action('rstr/settings/tab/content/transliteration', 'tab_content_transliteration');
		do_action('rstr/settings/tab/content/transliteration');
        break;
}