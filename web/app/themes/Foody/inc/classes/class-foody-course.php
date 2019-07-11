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

	public function get_floating_registration_button() {
		$floating_registration_button = get_field( 'floating_registration_button' );

		return $floating_registration_button;
	}

	public function get_information_top_section() {
		$information_top_sections = get_field( 'information_top_section' );

		return $information_top_sections;
	}

	public function get_information_bottom_section() {
		$information_bottom_section = get_field( 'information_bottom_section' );

		return $information_bottom_section;
	}

	public function get_information_registration_link() {
		$information_registration_link = get_field( 'information_info_registration_link' );

		return $information_registration_link;
	}

	public function get_course_is_for_title() {
		$course_is_for_title = get_field( 'course_is_for_title' );

		return $course_is_for_title;
	}

	public function get_course_is_for() {
		$course_is_for_bullets = get_field( 'course_is_for_bullets' );

		return $course_is_for_bullets;
	}

	public function get_how_it_works_title() {
		$how_it_works_title = get_field( 'how_it_works_title' );

		return $how_it_works_title;
	}

	public function get_how_it_works() {
		$how_it_works_steps = get_field( 'how_it_works_steps' );

		return $how_it_works_steps;
	}

	public function get_how_it_works_registration_link() {
		$how_it_works_steps_registration = get_field( 'how_it_works_steps_registration_link' );

		return $how_it_works_steps_registration;
	}
}