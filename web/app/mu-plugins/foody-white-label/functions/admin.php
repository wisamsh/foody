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
}
