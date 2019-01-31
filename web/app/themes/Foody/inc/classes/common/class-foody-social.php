<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/28/18
 * Time: 4:20 PM
 */
class Foody_Social
{

    private static $social_links = array(
        'facebook' => '',
        'instgram' => '',
        'youtube' => '',
        'pinterest' => '',
        'mail' => ''
    );

    /**
     * @param bool $echo
     *
     */
    public static function socials_bar($echo = true)
    {

    }

    public static function whatsapp($ext_classes = [])
    {
        $phone_number = get_option('whatsapp_phone_number');
        $url = "https://api.whatsapp.com/send?phone=$phone_number";
        if (!wp_is_mobile()) {
            $url = "https://web.whatsapp.com/send?phone=$phone_number";
        }

        $show = $phone_number = get_option('whatsapp_phone_number_toggle', false);

        $classes = $ext_classes;
        if (!$show) {
            $classes[] = 'invisible';
        }


        foody_get_template_part(get_template_directory() . '/template-parts/whatsapp-business.php', [
            'url' => $url,
            'classes' => $classes
        ]);
    }
}