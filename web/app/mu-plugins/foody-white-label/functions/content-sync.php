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
    update_option('foody_site_duplication_in_progress', true);
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
    add_action('wp_insert_post', 'foody_auto_sync_post', 10, 2);
    function foody_auto_sync_post($post_id, $post_object)
    {
        global $foody_auto_synced_post_types;

        if (!in_array($post_object->post_type, $foody_auto_synced_post_types)) {
            return;
        }

        // Check to see if we are autosaving
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if (in_array($post_object->post_status, array('auto-draft', 'inherit'))) {
            return;
        }

        // auto sync on main site only
        if (get_current_blog_id() != get_main_site_id()) {
            return;
        }

        $sites = get_sites(['site__not_in' => get_main_site_id()]);

        /** @var WP_Site $site */
        foreach ($sites as $site) {
            Foody_WhiteLabelDuplicator::duplicate($post_object, $site->blog_id);
        }
    }

    function foody_auto_sync_term($term_id, $tt_id, $taxonomy)
    {
        // auto sync on main site only
        if (get_current_blog_id() != get_main_site_id()) {
            return;
        }

        $term = get_term($term_id, $taxonomy);
        $sites = get_sites(['site__not_in' => get_main_site_id()]);

        /** @var WP_Site $site */
        foreach ($sites as $site) {
            Foody_WhiteLabelDuplicator::duplicateTerm($term, $site->blog_id);
        }
    }

    add_action('edit_term', 'foody_auto_sync_term', 10, 3);
}


add_action('wp_async_term_edit', 'foody_copy_posts_by_term', 10, 2);
function foody_copy_posts_by_term($term_id, $taxonomy)
{
    $duplicatedTerms = [
        'category',
        'post_tag'
    ];

    // if this taxonomy can be duplicated
    if (in_array($taxonomy, $duplicatedTerms)) {
        $sites = get_sites(['fields' => 'ids', 'site__not_in' => get_main_site_id()]);

        foreach ($sites as $site) {
            $term_duplication_key = "pass_data_$site";
            $copy = get_term_meta($term_id, $term_duplication_key, true);

            if (!empty($copy)) {
                $copied_to_key = "copied_to_$site";
                $copied = get_term_meta($term_id, $copied_to_key, true);

                // if not already copied
                if (empty($copied)) {
                    if ($taxonomy == 'post_tag') {
                        $result = Foody_WhiteLabelDuplicator::duplicateTag($term_id, $site);
                    } elseif ($taxonomy == 'category') {
                        $result = Foody_WhiteLabelDuplicator::duplicateCategory($term_id, $site);
                    }

                    if (!empty($result)) {
                        update_term_meta($term_id, $copied_to_key, true);
                    }
                }
            }
        }
    }
}
