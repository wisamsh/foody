<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 4:52 PM
 */

function copy_recipe()
{

}


add_action('add_meta_boxes', 'foody_add_content_sync_meta_box');

function foody_add_content_sync_meta_box()
{
    add_meta_box(
        'foody-content-sync',
        __('העתקת תוכן'),
        'foody_show_sync_meta_box',
        'foody_recipe',
        'side',
        'default'
    );
}

function foody_show_sync_meta_box(){

    foody_get_template_part(get_template_directory() . '/template-parts/content-sync.php');

//    echo 'hi';
}