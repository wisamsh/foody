<?php
/**
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 2019-05-13
 * Time: 16:38
 */

function foody_authors_page_script() {
	if ( get_page_type() == 'author' ) {
		$pixel_code = get_field( 'pixel_code' );
		if ( ! empty( $pixel_code ) ) {
			echo $pixel_code;
		}
	}
}

add_action( 'wp_head', 'foody_authors_page_script' );