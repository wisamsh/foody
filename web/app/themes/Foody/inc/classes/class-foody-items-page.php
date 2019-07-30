<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/2/19
 * Time: 7:28 PM
 */
class Foody_ItemsPage {

	public function cover() {
		$image = get_field( 'cover_image' );
		if ( ! empty( $image ) ) {
			foody_get_template_part( get_template_directory() . '/template-parts/content-cover-image.php', $image );
		}

	}

	public function items() {

		$items = get_field( 'items' );

		if ( ! empty( $items ) ) {

			foody_get_template_part( get_template_directory() . '/template-parts/content-items-page.php', [ 'items' => $items ] );

		}
	}
}