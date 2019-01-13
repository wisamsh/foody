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

    $size = 'large';
//    $size = [1100,733];

    return $size;
});


add_filter('wpseo_opengraph_image', function ($image) {

    return $image;
});

function foody_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'foody_mime_types');

function foody_fix_svg_thumb_display() {
    echo '
    td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail { 
      width: 100% !important; 
      height: auto !important; 
    }
  ';
}
add_action('admin_head', 'foody_fix_svg_thumb_display');
