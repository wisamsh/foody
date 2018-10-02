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