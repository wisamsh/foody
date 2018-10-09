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
    wp_redirect($redirect_url);
    exit;
}

add_action('user_register', 'auto_login_new_user');