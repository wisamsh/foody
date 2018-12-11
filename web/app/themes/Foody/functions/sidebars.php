<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/27/18
 * Time: 7:49 PM
 */


function foody_sidebars()
{


    $sidebars = array(
        array(
            'id' => 'foody-sidebar',
            'name' => 'Foody Sidebar',
        ),array(
            'id' => 'foody-sidebar-mobile',
            'name' => 'Foody Mobile Sidebar',
        ), array(
            'id' => 'homepage-categories',
            'name' => 'Homepage Categories',
        ), array(
            'id' => 'foody-social',
            'name' => 'Foody Social',
        )
    );

    foreach ($sidebars as $sidebar) {
        register_sidebar(array(
            'id' => $sidebar['id'],
            'name' => $sidebar['name'],
        ));
    }

}

add_action('widgets_init', 'foody_sidebars');



add_action( 'after_setup_theme', 'register_my_menu' );
function register_my_menu() {

    register_nav_menu('categories',__('תפריט קטגוריות'));
}
