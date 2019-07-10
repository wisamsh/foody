<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/6/18
 * Time: 8:35 PM
 */
class Foody_Course {

	/**
	 * Course constructor.
	 */
	public function __construct() {
	}

	public function get_cover_image() {
		$cover_image = get_field( 'cover_image' );

		return $cover_image;
	}

	public function get_main_video() {
		$video = get_field( 'video' );

		return $video;
	}

	public function get_floating_button() {
		$floating_registration_button = get_field( 'floating_registration_button' );

		return $floating_registration_button;
	}

	public function the_about() {
		$about = get_field( 'about' );

		echo $about;
	}

	public function get_information_top_section() {
		$information_top_sections = get_field( 'information_top_section' );

		$information_section = [];

		array_map( function ( $section ) use ( $information_section ) {
			$information_section[] = array(
				'title'    => $section['title'],
				'subtitle' => $section['subtitle']
			);
		}, $information_top_sections );

		return $information_section;
	}
}