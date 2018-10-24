<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/3/18
 * Time: 1:58 PM
 */


function foody_ajax_error($message = 'Error')
{

    return $message;
}

function foody_form_validation($required)
{
    $messages = null;
    if (!empty($required) && is_array($required)) {
        $messages = [];
        foreach ($required as $item) {
            if (!isset($_POST[$item])) {
                $messages[$item] = 'required';
            }
        }
    }

    return $messages;
}


require_once get_template_directory() . '/functions/ajax/comments.php';
require_once get_template_directory() . '/functions/ajax/favorites.php';
require_once get_template_directory() . '/functions/ajax/follow.php';
require_once get_template_directory() . '/functions/ajax/search.php';
require_once get_template_directory() . '/functions/ajax/load-more.php';
require_once get_template_directory() . '/functions/ajax/change-password.php';
//require_once get_template_directory() . '/functions/ajax/duplicate-titles.php';