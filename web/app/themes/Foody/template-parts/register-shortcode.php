<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/27/18
 * Time: 3:36 PM
 */

$action = get_query_var('registered', null);

$template = 'register-form.php';
$args = [];
if (!is_null($action) && is_user_logged_in()) {

    $user_id = get_current_user_id();

    $user = get_user_by('ID', $user_id);
    $template = 'login-welcome.php';
    $args = [
        'profile' => [
            'username' => $user->display_name
        ]
    ];
}

foody_get_template_part(get_template_directory() . "/template-parts/$template", $args);