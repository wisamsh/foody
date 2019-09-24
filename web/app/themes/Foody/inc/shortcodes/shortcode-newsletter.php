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

	$display = '<section class="newsletter">';
    $display = $display . do_shortcode('[contact-form-7 id="20190" title="ניוזלטר"]') . '</section>';


	return $display;
}

add_shortcode( 'foody-newsletter', 'foody_newsletter_shortcode' );
