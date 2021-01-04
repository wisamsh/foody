<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/26/19
 * Time: 5:24 PM
 */

function foody_get_background_image() {
    $queried_object_id = get_queried_object_id();
    if($queried_object_id) {
        $background_image = get_field('background_image', get_queried_object_id());
        if (empty($background_image)) {
            $post_id = get_the_ID();
            $feed_area_id = !empty($post_id) ? get_field('recipe_channel', $post_id) : get_field('recipe_channel');
            $feed_area_id = is_category() ? get_field('recipe_channel', get_queried_object()) : $feed_area_id;
            if (isset($_GET['referer']) || $feed_area_id ) {
                $referer_post = isset($_GET['referer']) ? $_GET['referer'] : $feed_area_id;
                if (!empty($referer_post)) {
                    if (is_category() || is_tag() || in_array(get_post_type(), [
                            'post',
                            'foody_recipe',
                            'foody_filter'
                        ])) {
                        $background_image = get_field('background_image', $referer_post);
                        $_SESSION['background_image'] = $background_image;
                    }
                }
            } else {
                $background_image = get_field('background_image', get_option('page_on_front'));
                if (!empty($background_image)) {
                    $_SESSION['background_image'] = $background_image;
                }
            }
        } else {
            $_SESSION['HTTP_REFERER'] = get_queried_object_id();
            $_SESSION['background_image'] = $background_image;
        }

        return $background_image;
    }
}