<?php

namespace MP\Modules;

use MP\Base;

class Acf_Config
{
    use Base;

    /**
     *  CONSTRUCT
     */
    public function __construct()
    {
        $this->add_theme_settings();
    }

    /**
     * Create acf admin page
     *
     * @return void
     */
    public function add_theme_settings()
    {
        if ( function_exists('acf_add_options_page') ) {
            acf_add_options_page([
                                     'page_title' => 'Theme Settings',
                                     'menu_title' => 'Theme Settings',
                                     'menu_slug'  => 'theme-general-settings',
                                     'capability' => 'edit_posts',
                                     'redirect'   => false
                                 ]);
        }
    }
}