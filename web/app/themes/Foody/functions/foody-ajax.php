<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/3/18
 * Time: 1:58 PM
 */

add_action('wp_ajax_ajaxcomments', 'foody_submit_ajax_comment');

function foody_submit_ajax_comment()
{
    /*
     *
     *
     * @since 4.4.0
     */
    $comment = wp_handle_comment_submission(wp_unslash($_POST));
    if (is_wp_error($comment)) {
        $error_data = intval($comment->get_error_data());
        if (!empty($error_data)) {
            wp_die('<p>' . $comment->get_error_message() . '</p>', __('Comment Submission Failure'), array('response' => $error_data, 'back_link' => true));
        } else {
            wp_die('Unknown error');
        }
    }

    /*
     * Set Cookies
     */
    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);

    /*
     * If you do not like this loop, pass the comment depth from JavaScript code
     */
    $comment_depth = 1;
    $comment_parent = $comment->comment_parent;
    while ($comment_parent) {
        $comment_depth++;
        $parent_comment = get_comment($comment_parent);
        $comment_parent = $parent_comment->comment_parent;
    }

    /*
     * Set the globals, so our comment functions below will work correctly
     */
    $GLOBALS['comment'] = $comment;
    $GLOBALS['comment_depth'] = $comment_depth;



    $template_args = array(
        'comment' => $comment,
        'depth' => $comment_depth
    );


    foody_get_template_part(get_template_directory() . '/template-parts/content-comment.php',$template_args);

//    echo $comment_html;

    die();

}