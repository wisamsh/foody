<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/24/18
 * Time: 12:06 PM
 */


function foody_change_password()
{
    $required = [
        'current_password',
        'password',
        'password_confirmation'
    ];

    $errors = foody_form_validation($required);

    if (!empty($errors)) {
        wp_send_json_error($errors, 400);
    }

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => "unauthorized"], 401);
    }

    $user = wp_get_current_user();

    $user_pass = $user->user_pass;

    $current_password = $_POST['current_password'];
    $new_password = $_POST['password'];

    require_once ABSPATH . 'wp-includes/class-phpass.php';
    $wp_hasher = new PasswordHash(8, true);

    if ($wp_hasher->CheckPassword($current_password, $user_pass)) {

        wp_set_password($new_password, $user->ID);
        wp_cache_delete($user->ID, 'users');
        wp_cache_delete($user->user_login, 'userlogins');
        wp_logout();
        $error = wp_signon(array('user_login' => $user->user_login, 'user_password' => $new_password), false);

        if (is_wp_error($error)) {
            wp_send_json_error($error, 400);
        } else {
            ob_start();
            wp_send_json_success();
        }

    } else {
        $error = [
            'message' => 'invalid password'
        ];

        wp_send_json_error($error, 400);
    }

}

add_action('wp_ajax_foody_change_password', 'foody_change_password');


