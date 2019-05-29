<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/27/18
 * Time: 2:36 PM
 */


function foody_register_shortcode($atts)
{

	$text = isset($atts['text']) ? $atts['text'] : '';
	if (empty($text)) {
		$text = __('הרשמו לאתר כדי להגיב, להעלות תמונות של הבישולים שלכם ולהרכיב לעצמם ספר מתכונים אישי עם כל המתכונים שהכי אהבתם.');
	}

	$display = foody_get_template_part( get_template_directory() . '/template-parts/register-shortcode.php', [
		'return' => true,
		'text'   => $text
	] );

    return $display;

}


add_shortcode('foody-register', 'foody_register_shortcode');
