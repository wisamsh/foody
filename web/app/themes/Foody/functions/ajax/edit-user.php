<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/24/18
 * Time: 12:06 PM
 */


function foody_edit_user()
{
    $required = [
        'first_name',
        'last_name'
    ];

    $validation_errors = foody_form_validation($required);


    $errors = new WP_Error();


    if (!is_user_logged_in()) {
        $errors->add(401, 'unauthorized');
    } else {
        if (!empty($validation_errors)) {
            $errors->add(400, 'bad request');
        } else {

            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];

            $user_data = new stdClass();

            $user_data->first_name = $first_name;
            $user_data->last_name = $last_name;

            $user_data->ID = get_current_user_id();


            $updated_user_id = wp_update_user($user_data);


            if (is_wp_error($updated_user_id)) {

                $errors->add(500, 'error updating user');

            } else {
                if (isset($_POST['phone_number'])) {
                    $result = update_user_meta($updated_user_id, 'phone_number', $_POST['phone_number']);
                    if (!$result) {
                        $errors->add(500, 'error updating phone number');
                    }
                }
            }

        }
    }

    if (!empty($errors->errors)) {
        wp_send_json_error($errors);
    } else {
        wp_send_json_success(get_user_by('ID', get_current_user_id()));
    }


}

add_action('wp_ajax_foody_edit_user', 'foody_edit_user');


function foody_edit_profile_picture()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not Authorized'], 401);
    }

    $id = get_current_user_id();

    $avatar = wp_handle_upload($_FILES['photo'], array(
        'test_form' => false,
        'unique_filename_callback' => 'wp_user_avatars_unique_filename_callback'
    ));

    if (empty($avatar)) {
        wp_send_json_error(['message' => 'Upload Failed'], 500);
    }

    if (function_exists('wp_user_avatars_update_avatar')) {
        wp_user_avatars_update_avatar($id, $avatar['url']);
        wp_send_json_success($avatar);
    } else {
        wp_send_json_error(['message' => 'User Avatars is Disabled'], 502);
    }

}

add_action('wp_ajax_foody_edit_profile_picture', 'foody_edit_profile_picture');


function foody_edit_user_approvals()
{

    $errors = new WP_Error();


    if (!is_user_logged_in()) {
        $errors->add(401, 'unauthorized');
    } else {

        $markting = isset($_POST['marketing']) ? true : false;
        $e_book = isset($_POST['e_book']) ? true : false;


        $ID = get_current_user_id();

        $resultMarketing = update_user_meta($ID, 'marketing', $markting);
        $resultMarketingEbook = update_user_meta($ID, 'e_book', $e_book);


        if ($resultMarketing === false || $resultMarketingEbook === false) {
            $errors->add(500, 'error updating user');
        }

    }

    if (!empty($errors->errors)) {
        wp_send_json_error($errors);
    } else {
        wp_send_json_success(get_user_by('ID', get_current_user_id()));
    }


}

add_action('wp_ajax_foody_edit_user_approvals', 'foody_edit_user_approvals');


