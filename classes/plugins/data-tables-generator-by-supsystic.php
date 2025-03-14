<?php

if (!defined('WPINC')) {
    die();
}

/**
 * Active Plugin: Data tables generator by supsystic
 *
 * @link              http://infinitumform.com/
 * @since             1.3.5
 * @package           Serbian_Transliteration
 * @author            Ivijan-Stefan Stipic
 */

class Transliteration_Plugin_Data_Tables_Generator_By_Supsystic extends Transliteration
{
    public function __construct()
    {
        $this->add_filter('transliteration_mode_filters', 'filters');
    }

    /**
     * @return mixed[]
     */
    public function filters($filters = []): array
    {
        $pfx = 'supsystic_tbl';

        return array_merge($filters, [
            $pfx . '_after_contactform_loaded' => 'content',
            // "{$pfx}_after_core_loaded" => 'content',
            // "{$pfx}_after_diagram_loaded" => 'content',
            // "{$pfx}_after_migrationfree_loaded" => 'content',
            // "{$pfx}_after_overview_loaded" => 'content',
            // "{$pfx}_after_promo_loaded" => 'content',
            // "{$pfx}_after_settings_loaded" => 'content',
            $pfx . '_after_tables_loaded' => 'content',
            // "{$pfx}_after_exporter_loaded" => 'content',
            // "{$pfx}_after_importer_loaded" => 'content',
            // "{$pfx}_after_migration_loaded" => 'content',
            $pfx . '_after_modules_loaded'           => 'content',
            $pfx . '_tables_get'                     => 'content',
            $pfx . '_before_table_render'            => 'content',
            $pfx . '_before_table_render_from_cache' => 'content',
        ]);
    }
}
