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
		if ( isset( $_SESSION['HTTP_REFERER'] ) ) {

			$referer_post = $_SESSION['HTTP_REFERER'];

			if ( ! empty( $referer_post ) ) {
				$post_type = get_post_type();
				if ( is_category() || in_array( $post_type, [ 'post', 'foody_recipe', 'foody_filter' ] ) ) {
					$background_image = get_field( 'background_image', $referer_post );
					$_SESSION['background_image'] = $background_image;
					if ( in_array( $post_type, [ 'post', 'foody_recipe', 'foody_filter' ] )) {
						unset($_SESSION['HTTP_REFERER']);
					}
				}
			}
		}
	} else {
		$_SESSION['HTTP_REFERER'] = get_queried_object_id();
		$_SESSION['background_image'] = $background_image;
	}

	return $background_image;
}