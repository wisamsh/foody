<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/26/19
 * Time: 5:24 PM
 */

function foody_get_background_image()
{
    $background_image = get_field('background_image', get_queried_object_id());
    if (empty($background_image)) {
        if (isset($_SERVER['HTTP_REFERER'])) {

            $referer_post = url_to_postid($_SERVER['HTTP_REFERER']);

            if (!empty($referer_post)) {
                $post_type = get_post_type();
                if (is_category() || in_array($post_type, ['post', 'foody_recipe','foody_filter'])) {
                    $background_image = get_field('background_image', $referer_post);
                }
            }
        }
    }

    return $background_image;
}