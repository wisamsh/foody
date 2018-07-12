<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/12/18
 * Time: 11:25 AM
 */


/**
 * @param $selector string field name. must be a field of type post (id or object)
 * @return WP_Post[] array of posts objects
 */function posts_to_array($selector)
{
    $posts = array();

    $posts_field = get_field($selector);

    foreach ($posts_field as $item) {
        if (is_int($item)) {
            $item = get_post($item);
        }

        $posts[] = $item;
    }

    return $posts;
}