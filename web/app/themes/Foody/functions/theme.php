<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/26/19
 * Time: 5:24 PM
 */

function foody_get_background_image() {

	$background_image = get_field( 'background_image', get_queried_object_id() );

	if ( empty( $background_image ) ) {

		if ( isset( $_COOKIE['bgReferrer'] ) ) {

			$referer_post = $_COOKIE['bgReferrer'];

			if ( ! empty( $referer_post ) ) {
				$post_type = get_post_type();
				if ( is_category() || in_array( $post_type, [ 'post', 'foody_recipe', 'foody_filter' ] ) ) {
					$background_image = get_field( 'background_image', $referer_post );

					if ( in_array( $post_type, [ 'post', 'foody_recipe', 'foody_filter' ] ) ) {
						unset( $_COOKIE['bgReferrer'] );
					}
				}
			}
		}
	} else {
		if ( ! isset( $_COOKIE['bgReferrer'] ) ) {
			setcookie( 'bgReferrer', get_queried_object_id(), ( time() + 3600 ), '/' );
		}
	}

	return $background_image;
}