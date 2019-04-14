<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 4:52 PM
 */

function copy_recipe($post_id)
{
    $title   = get_the_title($post_id);
    $oldpost = get_post($post_id);
    $post    = array(
        'post_title' => $title,
        'post_status' => 'publish',
        'post_type' => $oldpost->post_type,
        'post_author' => 1
    );
    $new_post_id = wp_insert_post($post);
    // Copy post metadata
    $data = get_post_custom($post_id);
    foreach ( $data as $key => $values) {
        foreach ($values as $value) {
            add_post_meta( $new_post_id, $key, $value );
        }
    }
    return $new_post_id;
}


add_action('add_meta_boxes', 'foody_add_content_sync_meta_box');
function foody_add_content_sync_meta_box()
{
    add_meta_box(
        'foody-content-sync',
        __('העתקת תוכן'),
        'foody_show_sync_meta_box',
        ['foody_recipe'],
        'side',
        'default'
    );
}

function foody_show_sync_meta_box()
{
    foody_get_template_part(PLUGIN_DIR . 'template-parts/content-sync.php');
}

add_action('edit_category_form_fields', 'foody_show_sync_fields');
function foody_show_sync_fields()
{
    ?>
    <fieldset disabled>

    <?php
    foody_get_template_part(PLUGIN_DIR . 'template-parts/content-sync.php',['wrap'=>'tr']);
    ?>

    </fieldset>
    <?php

}