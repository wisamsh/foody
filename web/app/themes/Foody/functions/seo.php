<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/17/19
 * Time: 3:17 PM
 */

add_filter('wpseo_robots', 'foody_handle_no_index');
function foody_handle_no_index($robots_str)
{
    $no_index = 'noindex,nofollow';

    if (is_single()) {
        $comments_regex = '/comment-page-[0-9]+(\/?)$/';
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];

            if (preg_match($comments_regex, $uri) != false) {
                $robots_str = $no_index;
            }
        }
    } elseif (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
        $robots_str = $no_index;
    }

    return $robots_str;
}