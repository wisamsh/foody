<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/10/18
 * Time: 2:55 PM
 */

function foody_ajax_rating()
{
    $logged_in = is_user_logged_in();
    if ($logged_in) {


        if (!isset($_POST['id']) || !isset($_POST['rating'])) {
            wp_send_json_error(['message' => 'bad request'], 400);
        }

        $id = $_POST['id'];

        $user_id = get_current_user_id();

        $rating = get_user_meta($user_id, 'rating', true);

        if (is_null($rating) || empty($rating)) {
            $rating = [];
        }

        $rating[$id] = $_POST['rating'];

        update_user_meta($user_id, 'rating', $rating);




    } else {
        wp_send_json_error(['message' => 'please log in'], 401);
    }


}

add_action('wp_ajax_rating', 'foody_ajax_rating');
add_action('wp_ajax_nopriv_rating', 'foody_ajax_rating');