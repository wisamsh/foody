<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 4:52 PM
 */

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
        foody_get_template_part(PLUGIN_DIR . 'template-parts/content-sync.php', ['wrap' => 'tr']);
        ?>

    </fieldset>
    <?php

}

if (is_main_site()) {
    function foody_update_post_in_sites($post_id)
    {
        // If this is just a revision, don't update.
        if (wp_is_post_revision($post_id)) {
            return;
        }

        $synced_post_types = [
            'foody_ingredient',
            'foody_recipe',
            'post'
        ];

        $current_post_type = get_post_type($post_id);

        if (!in_array($current_post_type, $synced_post_types)) {
            return;
        }


    }

    add_action('save_post', 'foody_update_post_in_sites');
}

add_action('wp_async_wpmu_new_blog', 'foody_do_duplicate_site', 10, 1);
function foody_do_duplicate_site($blog_id)
{
    $max_execution_time = ini_get('max_execution_time');
    ini_set('max_execution_time', 300);
    Foody_WhiteLabelDuplicator::whiteLabelCreate($blog_id);
    ini_set('max_execution_time', $max_execution_time);
}

if (is_main_site()) {
    try {
        new Foody_WhiteLabelTermDuplicatorTask();
        new Foody_WhiteLabelAuthorDuplicatorTask();
    } catch (Exception $e) {
        Foody_WhiteLabelLogger::exception($e);
    }
}