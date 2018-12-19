<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/20/18
 * Time: 11:03 PM
 */


function get_logo_with_size($width, $height)
{
    $custom_logo_id = get_theme_mod('custom_logo');
    $url = home_url();
    $html = sprintf('<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
        esc_url($url),
        wp_get_attachment_image($custom_logo_id, array($width, $height), false, array(
            'class' => 'custom-logo',
        ))
    );

    return $html;
}

add_theme_support('post-thumbnails');

$sizes = [
    [
        'width' => 1100,
        'height' => 733.33,
        'name' => 'foody-main'
    ],
    [
        'width' => 355,
        'height' => 236.666666666666667,
        'name' => 'list-item'
    ],
];

foreach ($sizes as $size) {
    add_image_size($size['name'], $size['width'], $size['height'], true);
}

add_filter('wpseo_opengraph_image_size', function ($size) {

    $size = 'list-item';
//    $size = 355;

    return $size;
});


add_filter('wpseo_opengraph_image', function ($image) {

    return $image;
});
