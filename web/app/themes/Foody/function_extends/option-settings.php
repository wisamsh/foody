<?php 
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
    'page_title' => 'פודי הגדרות כלליות וחגים',
    'menu_title' => 'הגדרות כלליות וחגים',
    'menu_slug' => 'foody-general-settings',
    'capability' => 'edit_posts',
    'redirect' => false
    ));
    
    //acf_add_options_sub_page(array(
    // 'page_title' => 'Theme Header Settings',
    // 'menu_title' => 'Header',
    // 'parent_slug' => 'foody-general-settings',
    //));
    
    }
?>