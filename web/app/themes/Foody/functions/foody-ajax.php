<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/3/18
 * Time: 1:58 PM
 */


function foody_ajax_error($message = 'Error'){

    return $message;
}


require_once get_template_directory() . '/functions/ajax/comments.php';
require_once get_template_directory() . '/functions/ajax/favorites.php';
require_once get_template_directory() . '/functions/ajax/follow.php';
require_once get_template_directory() . '/functions/ajax/search.php';
//require_once get_template_directory() . '/functions/ajax/duplicate-titles.php';