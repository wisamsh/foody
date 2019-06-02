<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/29/19
 * Time: 12:37 PM
 */

add_action('admin_head', 'foody_block_admin_if_need', 1);

function foody_block_admin_if_need()
{
    $duplication_in_progress = get_option('foody_site_duplication_in_progress');

    if ($duplication_in_progress) {
        ?>
        <style>
            .overlay {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100vw;
                height: 100vh;
                background-color: white;
                z-index: 999999999999;
                cursor: progress;
                text-align: center;
                font-size: 28px;
                line-height: 100vh;
            }
        </style>
        <script>
            jQuery(document).ready(function ($) {
                jQuery('body').append('<div id="foody-blocker" onclick="return false;" class="overlay">\n' +
                    '            <section class="message">\n' +
                    '                    מעתיק תוכן, אנא המתן.\n' +
                    '            </section>\n' +
                    '        </div>');

                var maxTries = 300, currentTry = 0;
                var startTime = new Date().getTime();

                function checkProgressStatus() {
                    currentTry++;
                    $.ajax(ajaxurl + '?action=foody_duplication_progress').success(function (data) {

                        // data.data.in_progress = Date.now() > (startTime + (1000 * 5) );
                        if (data.data.in_progress && currentTry < maxTries) {
                            setTimeout(function () {
                                checkProgressStatus();
                            }, 1000);
                        } else {
                            $('#foody-blocker').remove();
                        }
                    });
                }

                checkProgressStatus();
            });


        </script>

        <?php
    }
}

add_action('wp_ajax_foody_duplication_progress', 'foody_duplication_progress');
function foody_duplication_progress()
{
    $duplication_in_progress = get_option('foody_site_duplication_in_progress');
    wp_send_json_success(['in_progress' => $duplication_in_progress]);
}


if (is_main_site()) {

    add_action('add_meta_boxes_foody_recipe', 'add_post_mapping_box');
    add_action('add_meta_boxes_post', 'add_post_mapping_box');

    /**
     * @param $post WP_Post
     */
    function add_post_mapping_box($post)
    {

        add_meta_box(
            'foody-post-mapping',
            __('מופעים באתרים נוספים'),
            'foody_show_post_mappings',
            $post->post_type,
            'side',
            'high'
        );
    }

    /**
     * Shows the list of occurrences of
     * this post in other blogs.
     * Shows a list of edit links to the post in the relevant blogs.
     */
    function foody_show_post_mappings()
    {
        global $post;

        $sites = Foody_WhiteLabelPostMapping::getByPost($post->ID);
        if (!empty($sites)) {
            ?>
            <ul>
                <?php foreach ($sites as $site):

                    // sub site id
                    $blog_id = $site['blog_id'];

                    switch_to_blog($blog_id);

                    $link = admin_url('post.php?post=' . $site['destination_post_id']) . '&action=edit';

                    switch_to_blog(get_main_site_id());

                    ?>
                    <li>
                        <a href="<?php echo $link ?>">
                            <?php
                            printf(__('צפה בתוכן זה ב- %s'), (get_site($blog_id))->blogname);
                            ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php
        } else {
            ?>
            <b>
                <?php echo __('תוכן זה לא קיים באתר משנה') ?>
            </b>
            <?php
        }
    }


//    add_action('add_meta_boxes_foody_recipe', 'add_post_duplication_box');
//    add_action('add_meta_boxes_post', 'add_post_duplication_box');

    /**
     * @param $post WP_Post
     */
    function add_post_duplication_box($post)
    {

        add_meta_box(
            'foody-post-duplication',
            __('העתק לאתר משנה'),
            'add_post_duplication_box_cb',
            $post->post_type,
            'side',
            'high'
        );
    }

    function add_post_duplication_box_cb()
    {
        global $post;
        $sites_for_post = Foody_WhiteLabelPostMapping::getByPost($post->ID);

        if (!empty($sites_for_post)) {
            $sites_for_post = array_map(function ($site) {
                return isset($site['blog_id']) ? $site['blog_id'] : null;
            }, $sites_for_post);
        } else {
            $sites_for_post = [];
        }

        $excluded_sites = array_merge($sites_for_post, [get_main_site_id()]);

        $sites = get_sites(['site__not_in' => $excluded_sites]);

        $copied_to_all = count($sites) == 0;
        /** @var WP_Site $site */
        foreach ($sites as $site) {
            ?>
            <label for="<?php echo $site->blog_id ?>">
                <?php echo $site->blogname ?>
            </label>
            <input id="<?php echo $site->blog_id ?>" name="copy_to_<?php echo $site->blog_id ?>"
                   type="checkbox">
            <?php
        }

        if ($copied_to_all) {
            echo __('פוסט זה כבר קיים בכל אתרי המשנה');
        }
    }
}


function foody_admin_users_caps($caps, $cap, $user_id, $args)
{

    foreach ($caps as $key => $capability) {

        if ($capability != 'do_not_allow')
            continue;

        switch ($cap) {
            case 'edit_user':
            case 'edit_users':
                $caps[$key] = 'edit_users';
                break;
            case 'delete_user':
            case 'delete_users':
                $caps[$key] = 'delete_users';
                break;
            case 'create_users':
                $caps[$key] = $cap;
                break;
        }
    }

    return $caps;
}

add_filter('map_meta_cap', 'foody_admin_users_caps', 1, 4);
remove_all_filters('enable_edit_any_user_configuration');
add_filter('enable_edit_any_user_configuration', '__return_true');

/**
 * Checks that both the editing user and the user being edited are
 * members of the blog and prevents the super admin being edited.
 */
function foody_edit_permission_check()
{
    global $current_user, $profileuser;

    $screen = get_current_screen();

    wp_get_current_user();

    if (!is_super_admin($current_user->ID) && in_array($screen->base, array('user-edit', 'user-edit-network'))) { // editing a user profile
        if (is_super_admin($profileuser->ID)) { // trying to edit a superadmin while less than a superadmin
            wp_die(__('You do not have permission to edit this user.'));
        } elseif (!(is_user_member_of_blog($profileuser->ID, get_current_blog_id()) && is_user_member_of_blog($current_user->ID, get_current_blog_id()))) { // editing user and edited user aren't members of the same blog
            wp_die(__('You do not have permission to edit this user.'));
        }
    }

}

add_filter('admin_head', 'foody_edit_permission_check', 1, 4);