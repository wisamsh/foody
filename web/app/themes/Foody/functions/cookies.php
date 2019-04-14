<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/8/19
 * Time: 8:49 PM
 */

function foody_set_background_image_cookie() {
//	if ( ! isset( $_COOKIE['bg-referrer'] ) ) {
//
////		var_dump( get_queried_object_id() );
//		setcookie( 'bgReferrer', get_queried_object_id(), ( time() + 3600 ), '/' );
////		session_regenerate_id( true );
//	}
}

add_action( 'init', 'foody_set_background_image_cookie' );