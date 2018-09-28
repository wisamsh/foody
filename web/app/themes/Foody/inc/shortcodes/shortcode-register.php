<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/27/18
 * Time: 2:36 PM
 */


function foody_register_shortcode($atts)
{


    $disaply = foody_get_template_part(get_template_directory() . '/template-parts/register-shortcode.php', ['return' => true]);

    return $disaply;

}


add_shortcode('foody-register', 'foody_register_shortcode');
