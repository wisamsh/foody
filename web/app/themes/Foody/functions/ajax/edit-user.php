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


