<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/6/18
 * Time: 8:35 PM
 */
class Foody_Campaign {

	private $link;
	private $image_link;
	public $show_social = false;
	public $lower_link;
	public $registered_user_link;
	public $content_link;

	/**
	 * E-Book constructor.
	 */
	public function __construct() {
		$this->show_social          = get_field( 'show_social' );
		$this->lower_link           = get_field( 'lower_link' );
		$this->registered_user_link = get_field( 'registered_user_link' );
		$this->content_link         = get_field( 'content_link' );
	}

	public function has_hero_video() {
		$video = get_field( 'hero_video' );

		return ! empty( $video );
	}

	public function the_hero_video() {
		$video_url = get_field( 'hero_video' );

		if ( $video_url && count( $parts = explode( 'v=', $video_url ) ) > 1 ) {

			$query    = explode( '&', $parts[1] );
			$video_id = $query[0];
			$args     = array(
				'id'      => $video_id,
				'post_id' => ''
			);
			foody_get_template_part( get_template_directory() . '/template-parts/content-recipe-video.php', $args );
		}
	}

	public function get_hero_image_url() {
		$image = get_field( 'hero_image' );
		if ( empty( $image ) ) {
			$image = $GLOBALS['images_dir'] . 'e-book.png';
		} else {
			$image = $image['url'];
		}

		return $image;
	}

	public function get_mobile_hero_image_url() {
		$image = get_field( 'mobile_hero_image' );
		if ( empty( $image ) ) {
			$image = $GLOBALS['images_dir'] . 'e-book-mobile.png';
		} else {
			$image = $image['url'];
		}

		return $image;
	}

	public function get_hero_link_url() {
		$link = get_field( 'hero_link' );
		if ( empty( $link ) ) {
			$link = [
				'url'    => get_permalink( get_page_by_title( 'הרשמה' ) ),
				'target' => '_self'
			];
		}

		return $link;
	}

	public function get_promoted_images() {
		$images = get_field( 'images' );
		if ( empty( $images ) ) {
			$images = [
				[
					'image'    => [ 'url' => $GLOBALS['images_dir'] . 'pavlove.png' ],
					'title'    => 'פבלובה של מיקי שמו',
					'subtitle' => 'עוגת מרנג עם קציפת וניל ופירות',
				],
				[
					'image'    => [ 'url' => $GLOBALS['images_dir'] . 'kinoa.png' ],
					'title'    => 'סלט קינואה של שר פיטנס',
					'subtitle' => 'סלט קינואה עם עשבי תיבול,חמוציות ואגוזים',
				],
				[
					'image'    => [ 'url' => $GLOBALS['images_dir'] . 'salmon.png' ],
					'title'    => 'סלמון של יונית צוקרמן',
					'subtitle' => 'דג סלמון ברוטב מרוקאי חריף',
				]
			];
		}

		return $images;
	}

}