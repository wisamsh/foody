<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/18/19
 * Time: 10:21 AM
 */


add_action('wp_ajax_load_homepage_content', 'foody_load_homepage_content');
add_action('wp_ajax_nopriv_load_homepage_content', 'foody_load_homepage_content');

function foody_load_homepage_content()
{
    $homepage = new Foody_HomePage();

    $homepage->feed();

    die();
}