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
		if ( isset( $_GET['referer'] ) ) {
			$referer_post = $_GET['referer'];
			if ( ! empty( $referer_post ) ) {
				if ( is_category() || is_tag() || in_array( get_post_type(), [
						'post',
						'foody_recipe',
						'foody_filter'
					] ) ) {
					$background_image             = get_field( 'background_image', $referer_post );
					$_SESSION['background_image'] = $background_image;
				}
			}
		} else {
			$background_image = get_field( 'background_image', get_option( 'page_on_front' ) );
			if ( ! empty( $background_image ) ) {
				$_SESSION['background_image'] = $background_image;
			}
		}
	} else {
		$_SESSION['HTTP_REFERER']     = get_queried_object_id();
		$_SESSION['background_image'] = $background_image;
	}

	return $background_image;
}