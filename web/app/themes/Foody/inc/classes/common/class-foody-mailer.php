<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 6:04 PM
 */

class Foody_Mailer
{


    public static function send($subject, $body, $to, $is_html = true)
    {
        $headers = [];
        if ($is_html) {
            $body = foody_get_template_part(get_template_directory() . "/email-templates/$body.php", ['subject' => $subject, 'return' => true]);
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $GLOBALS["use_html_content_type"] = true;
        }

        wp_mail($to, $subject, $body, $headers);
    }
}