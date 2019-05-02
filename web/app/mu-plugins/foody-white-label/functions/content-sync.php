<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 4:52 PM
 */

// async blog creation hook
add_action('wp_async_wpmu_new_blog', 'foody_do_duplicate_site', 10, 1);
/**
 * Copy all relevant content to a newly created blog
 * @param $blog_id
 */
function foody_do_duplicate_site($blog_id)
{
    update_option('foody_site_duplication_in_progress', true);
    $max_execution_time = ini_get('max_execution_time');
    ini_set('max_execution_time', 300);
    Foody_WhiteLabelDuplicator::whiteLabelCreate($blog_id);
    ini_set('max_execution_time', $max_execution_time);
}

if (is_main_site()) {

    // load async tasks
    add_action('plugins_loaded', 'foody_init_async_tasks');
    /**
     * Initializes async operations that take place
     * on blog creation and content edit.
     * Hooked into 'plugins_loaded'
     * @see WP_Async_Task
     */
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

    // auto sync core Foody post types
    add_action('wp_insert_post', 'foody_auto_sync_post', 10, 2);
    /**
     * Automatically create/update a post in all blogs
     * when created/updated in main site.
     *
     * @see $foody_auto_synced_post_types
     * @param $post_id
     * @param $post_object
     */
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

    // auto sync core Foody taxonomies
    add_action('edit_term', 'foody_auto_sync_term', 10, 3);
    /**
     * Automatically create/update a custom taxonomy in all blogs
     * when created/updated in main site.
     * @see $foody_auto_synced_taxonomies
     * @param $term_id
     * @param $tt_id
     * @param $taxonomy
     */
    function foody_auto_sync_term($term_id, $tt_id, $taxonomy)
    {
        global $foody_auto_synced_taxonomies;

        if (!in_array($taxonomy, $foody_auto_synced_taxonomies)) {
            return;
        }

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

    // copy term posts
    add_action('wp_async_edit_term', 'foody_copy_posts_by_term', 10, 2);
    /**
     * Copy posts by term to a specific blog
     * @param $term_id
     * @param $taxonomy
     */
    function foody_copy_posts_by_term($term_id, $taxonomy)
    {
        $duplicatedTerms = [
            'category',
            'post_tag'
        ];

        // if this taxonomy can be duplicated
        if (in_array($taxonomy, $duplicatedTerms)) {

            $sites_to_copy_to = get_field('sites', "{$taxonomy}_$term_id");

            if (!empty($sites_to_copy_to)) {

                foreach ($sites_to_copy_to as $site_to_copy_to) {
                    $blog_id = $site_to_copy_to['foody_sites'];
                    $copied_to_key = "copied_to_$blog_id";
                    $copied = get_term_meta($term_id, $copied_to_key, true);
                    if (empty($copied)) {
                        if ($taxonomy == 'post_tag') {
                            $result = Foody_WhiteLabelDuplicator::duplicateTag($term_id, $blog_id, $site_to_copy_to);
                        } elseif ($taxonomy == 'category') {
                            $result = Foody_WhiteLabelDuplicator::duplicateCategory($term_id, $blog_id, $site_to_copy_to);
                        }

                        if (!empty($result['success'])) {
                            update_term_meta($term_id, $copied_to_key, true);
                        }
                    }
                }
            }
        }
    }

    // Copy author posts
    add_action('wp_async_edit_user_profile_update', 'foody_copy_posts_by_author');
    /**
     * Copy posts by authors to a specific blog
     * @param $user_id
     */
    function foody_copy_posts_by_author($user_id)
    {
        $user = get_user_by('ID', $user_id);

        $duplicatedRoles = [
            'author'
        ];

        // run duplication only for relevant user roles
        $user_has_relevant_role = count(array_intersect($user->roles, $duplicatedRoles)) > 0;
        if ($user_has_relevant_role) {

            // get custom field
            $sites_to_copy_to = get_field_object('sites', "user_$user_id");

            if (!empty($sites_to_copy_to)) {

                foreach ($sites_to_copy_to as $site_to_copy_to) {

                    // see ./acf.php for info about the
                    // 'foody_sites' key with acf select field
                    // and the dynamic population of the field
                    $blog_id = $site_to_copy_to['foody_sites'];

                    $copied_to_key = "copied_to_$blog_id";

                    $copied = get_user_meta($user_id, $copied_to_key, true);

                    // don't run if already copied
                    if (empty($copied)) {
                        $result = Foody_WhiteLabelDuplicator::duplicateAuthor($user_id, $blog_id, $site_to_copy_to);
                        if (!empty($result['success'])) {
                            update_user_meta($user_id, $copied_to_key, true);
                        }
                    }
                }

            }
        }

    }

}



