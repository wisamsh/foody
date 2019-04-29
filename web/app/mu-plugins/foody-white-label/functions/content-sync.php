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

// async blog creation hook
add_action('wp_async_wpmu_new_blog', 'foody_do_duplicate_site', 10, 1);
function foody_do_duplicate_site($blog_id)
{
    update_option('foody_site_duplication_in_progress',true);
    $max_execution_time = ini_get('max_execution_time');
    ini_set('max_execution_time', 300);
    Foody_WhiteLabelDuplicator::whiteLabelCreate($blog_id);
    ini_set('max_execution_time', $max_execution_time);
}

// load async tasks
if (is_main_site()) {
    function foody_init_async_tasks()
    {
        try {
            new Foody_WhiteLabelDuplicatorTask();
            new Foody_WhiteLabelTermDuplicatorTask();
            new Foody_WhiteLabelAuthorDuplicatorTask();
        } catch (Exception $e) {
            Foody_WhiteLabelLogger::exception($e);
        }
    }

    add_action('plugins_loaded', 'foody_init_async_tasks');
}

if (is_main_site()) {

}

