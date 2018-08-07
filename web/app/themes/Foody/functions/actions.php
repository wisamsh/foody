<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/4/18
 * Time: 4:32 PM
 */

add_action('pre_get_comments', function (\WP_Comment_Query $query) {
    /* only allow 'my_custom_comment_type' when is required explicitly */
    if ($query->query_vars['type'] !== 'how_i_did') {
        $query->query_vars['type__not_in'] = array_merge(
            (array)$query->query_vars['type__not_in'],
            array('how_i_did')
        );
    }
});


/*
*   Restrict non logged users to certain pages
*/

add_action('template_redirect', 'my_non_logged_redirect');
function my_non_logged_redirect()
{
    $restricted_pages = array(
        'פרופיל-אישי'
    );

    $slug = urldecode(get_post_field('post_name', get_post()));

    if (in_array($slug, $restricted_pages) && !is_user_logged_in()) {
        wp_redirect(home_url());
        die();
    }
}