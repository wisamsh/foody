<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/20/18
 * Time: 11:03 PM
 */


function get_logo_with_size($width,$height){
    $custom_logo_id = get_theme_mod('custom_logo');
    $url = network_site_url();
    $html = sprintf('<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
        esc_url($url),
        wp_get_attachment_image($custom_logo_id, array($width, $height), false, array(
            'class' => 'custom-logo',
        ))
    );

    return $html;
}