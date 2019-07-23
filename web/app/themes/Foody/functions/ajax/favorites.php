<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/22/18
 * Time: 8:13 PM
 */

// this line allows logged out users to send the request
//add_action( 'wp_ajax_nopriv_toggle_favorite', 'foody_toggle_favorite' );
add_action( 'wp_ajax_toggle_favorite', 'foody_toggle_favorite' );

function foody_toggle_favorite() {

	if ( ! is_user_logged_in() ) {
		wp_die( foody_ajax_error( 'not logged in' ) );
	}

	$post_id = $_POST['post_id'];
	$user_id = get_current_user_id();

	$favorites = get_user_meta( $user_id, 'favorites', true );

	if ( empty( $favorites ) ) {
		$favorites = [];
	}

	$index = array_search( $post_id, $favorites );
	if ( $index !== false ) {
		unset( $favorites[ $index ] );
	} else {
		$favorites[] = $post_id;
	}

	update_user_meta( $user_id, 'favorites', $favorites );

	$_SESSION['favorites'] = get_user_meta( $user_id, 'favorites', true );


	die(); // don't forget this thing if you don't want "0" to be displayed
}