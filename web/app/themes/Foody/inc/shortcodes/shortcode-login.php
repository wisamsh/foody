<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/29/18
 * Time: 2:38 PM
 */


function foody_login_shortcode($atts)
{


    $display = foody_get_template_part(get_template_directory() . '/template-parts/login-shortcode.php', ['return' => true]);

    return $display;

}


add_shortcode('foody-login', 'foody_login_shortcode');