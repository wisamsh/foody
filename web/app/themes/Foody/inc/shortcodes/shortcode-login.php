<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/29/18
 * Time: 2:38 PM
 */


function foody_login_shortcode($atts)
{

	$text = isset($atts['text']) ? $atts['text'] : '';
	if (empty($text)) {
		$text = __('התחברו ותתחילו להנות ממגוון עצום של תכנים קולינריים ומתכונים עם אלפי שעות וידאו, להרכיב לעצמכם ספר מתכונים אישי עם המתכונים שהכי אהבתם, לשתף בתמונות, להגיב ולשאול שאלות.');
	}

	$display = foody_get_template_part( get_template_directory() . '/template-parts/login-shortcode.php', [
		'return' => true,
		'text'   => $text
	] );

    return $display;

}


add_shortcode('foody-login', 'foody_login_shortcode');