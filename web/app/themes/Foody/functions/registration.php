<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/2/18
 * Time: 11:12 AM
 */


function wsl_change_default_permissons($provider_scope, $provider)
{
    if ('facebook' == strtolower($provider)) {
        $provider_scope = 'email, public_profile';
    }

    return $provider_scope;
}

add_filter('wsl_hook_alter_provider_scope', 'wsl_change_default_permissons', 10, 2);


/**
 * Automatically logs the user in
 * after registration
 * @param int $user_id
 */
function auto_login_new_user($user_id)
{
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    $redirect_url = home_url('הרשמה');
    $redirect_url = add_query_arg('registered', true, $redirect_url);

    Foody_Analytics::get_instance()->user_register();

    wp_redirect($redirect_url);

    exit;
}

add_action('user_register', 'auto_login_new_user');


function foody_user_login($user_login, $user)
{

    if (user_can($user, 'subscriber')) {

        Foody_Analytics::get_instance()->event('login', [
            'email' => $user->user_email
        ]);
    }
}


add_action('wp_login', 'foody_user_login', 10, 2);

function foody_user_logout()
{
    $user = wp_get_current_user();

    if (user_can($user, 'subscriber')) {

        Foody_Analytics::get_instance()->event('logout', [
            'email' => $user->user_email
        ]);
    }
}


add_action('clear_auth_cookie', 'foody_user_logout', 10, 2);


function social_login_redirect($user_id, $provider, $hybridauth_user_profile, $redirect_to)
{
    $redirect_to = $redirect_to;
}

add_action('wsl_hook_process_login_before_wp_safe_redirect', 'social_login_redirect',10,4);