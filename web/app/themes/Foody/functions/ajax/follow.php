<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/2/18
 * Time: 8:39 PM
 */

add_action('wp_ajax_toggle_follow', 'foody_toggle_follow');


function foody_toggle_follow()
{
    $allowed_topics = [
        'followed_authors',
        'followed_channels',
        'followed_feed_channels'
    ];

    if (!is_user_logged_in()) {
        wp_die(foody_ajax_error('not logged in'));
    }

    $topic_id = $_POST['topic_id'];
    $topic = $_POST['topic'];


    if (!in_array($topic, $allowed_topics)) {

        $res = [
            'error' => [
                'message' => "invalid topic $topic"
            ]
        ];

        echo json_encode($res);
        die();
    }

    $user_id = get_current_user_id();

    $topics = get_user_meta($user_id, $topic, true);

    if (empty($topics)) {
        $topics = [];
    }

    $index = array_search($topic_id, $topics);
    if ($index !== FALSE) {
        unset($topics[$index]);
    } else {
        $topics[] = $topic_id;
    }

    update_user_meta($user_id, $topic, $topics);

    global $wp_session;

    $wp_session[$topic] = get_user_meta($user_id, $topic, true);


    die();
}