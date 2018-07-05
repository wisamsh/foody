<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/4/18
 * Time: 4:32 PM
 */


add_action('wp_ajax_cloadmore', 'foody_comments_loadmore_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_cloadmore', 'foody_comments_loadmore_handler'); // wp_ajax_nopriv_{action}

function foody_comments_loadmore_handler()
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