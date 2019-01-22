<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/22/19
 * Time: 4:24 PM
 */

function foody_sitemap_shortcode($atts)
{

    $display = foody_get_template_part(get_template_directory() . '/template-parts/common/sitemap.php', ['return' => true]);

    return $display;

}


add_shortcode('foody-sitemap', 'foody_sitemap_shortcode');