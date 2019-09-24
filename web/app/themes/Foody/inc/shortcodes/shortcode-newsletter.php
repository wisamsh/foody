<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 24/9/19
 * Time: 10:13 AM
 */

function foody_newsletter_shortcode( $attrs ) {
	if ( empty( $attrs ) ) {
		$attrs = [];
	}
	$attrs['return'] = true;

	$display = '<section class="newsletter">[contact-form-7 id="3101" title="ניוזלטר"]</section>';

	return $display;
}

add_shortcode( 'foody-newsletter', 'foody_newsletter_shortcode' );
