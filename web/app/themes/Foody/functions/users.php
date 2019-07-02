<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/26/19
 * Time: 4:21 PM
 */

function foody_api_user_role()
{
    $role_name = 'foody_api_user';
    add_role(
        $role_name,
        'Foody API User',
        ['read']
    );

    $role = get_role($role_name);
    $role->add_cap('access_foody_api', true);
}

add_action('init', 'foody_api_user_role');