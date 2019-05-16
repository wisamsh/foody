<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/22/18
 * Time: 8:22 PM
 */

add_action('wp_ajax_ajaxcomments', 'foody_submit_ajax_comment');
add_action('wp_ajax_nopriv_ajaxcomments', 'foody_submit_ajax_comment');
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

            wp_send_json_error([
                'message' => $comment->get_error_message(),
            ], 400);
//            wp_die('<p>' . $comment->get_error_message() . '</p>', __('Comment Submission Failure'), array('response' => $error_data, 'back_link' => true));
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
        'depth' => $comment_depth,
        'max_depth' => get_option('thread_comments_depth'),
        'reply_text' => __('הוסף תגובה', 'Foody')
    );


    foody_get_template_part(get_template_directory() . '/template-parts/content-comment.php', $template_args);

    die();

}


add_action('wp_ajax_cloadmore', 'foody_comments_load_more_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_cloadmore', 'foody_comments_load_more_handler'); // wp_ajax_nopriv_{action}
function foody_comments_load_more_handler()
{

    // maybe it isn't the best way to declare global $post variable, but it is simple and works perfectly!
    global $post;
    $post = get_post($_POST['post_id']);
    setup_postdata($post);

    // actually we must copy the params from wp_list_comments() used in our theme
    $foody_comments = new Foody_Comments();

    $args = $foody_comments->get_list_comments_args();

    $args['page'] = $_POST['cpage'];

    $foody_comments->list_comments($args);


    die; // don't forget this thing if you don't want "0" to be displayed
}

add_action('wp_ajax_ajaxhow_i_did', 'foody_submit_ajax_how_i_did');

function foody_submit_ajax_how_i_did()
{

    $time = current_time('mysql');


    $user = wp_get_current_user();
    $image = media_handle_upload('attachment', get_the_ID());

    if (is_wp_error($image)) {
        wp_die('bad file');
    }

    $data = array(
        'comment_post_ID' => $_POST['post_id'],
        'comment_author' => $user->user_login,
        'comment_author_email' => $user->user_email,
        'comment_content' => $_POST['comment'],
        'comment_type' => 'how_i_did',
        'comment_parent' => $_POST['comment_parent'],
        'user_id' => get_current_user_id(),
        'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
        'comment_date' => $time,
        'comment_meta' => array(
            'attachment' => $image
        ),
	    'comment_approved' => 0
    );


    $comment_id = wp_insert_comment($data);

    if (!$comment_id) {
        wp_die('comment insert failed');
    }

    $comment = get_comment($comment_id, ARRAY_A);

    foody_get_template_part(get_template_directory() . '/template-parts/content-comment-how-i-did.php', $comment);


    die();

}

add_action('wp_ajax_hidloadmore', 'foody_hid_loadmore_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_hidloadmore', 'foody_hid_loadmore_handler'); // wp_ajax_nopriv_{action}
function foody_hid_loadmore_handler()
{

    // maybe it isn't the best way to declare global $post variable, but it is simple and works perfectly!
    global $post;
    $post = get_post($_POST['post_id']);
    setup_postdata($post);

    // actually we must copy the params from wp_list_comments() used in our theme
    $foody_comments = new Foody_HowIDid();

    $args = $foody_comments->get_args();

    $current_page = intval($_POST['hidpage']);

    $max_pages = $foody_comments->get_page_count();

    $current_page = $max_pages - $current_page;

    $per_page = intval(get_option('hid_per_page'));
    $args['offset'] = $current_page * $per_page;

    $foody_comments->the_comments($args);


    die; // don't forget this thing if you don't want "0" to be displayed
}