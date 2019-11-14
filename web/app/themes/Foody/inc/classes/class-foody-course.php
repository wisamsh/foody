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

	public function get_mobile_cover_image() {
		$mobile_cover_image = get_field( 'mobile_cover_image' );

		return $mobile_cover_image;
	}

	public function get_html_area() {
        $html_area = get_field( 'html_area' );

        return $html_area;
    }

    public function get_html_area_mobile() {
        $html_area = get_field( 'html_area_mobile' );

        return $html_area;
    }

	public function get_main_image() {
		$main_image = get_field( 'main_image' );

		return $main_image;
	}

	public function get_floating_registration_button() {
		$floating_registration_button = get_field( 'floating_registration_button' );

		return $floating_registration_button;
	}

	public function has_information() {
		return (
			! empty( $this->get_information_top_section() ) &&
			! empty( $this->get_information_bottom_section() ) &&
			! empty( $this->get_information_registration_link() )
		);
	}

	public function get_information_top_section() {
		$information_top_sections = get_field( 'information_top_section' );

		return $information_top_sections;
	}

	public function get_information_bottom_section() {
		$information_bottom_section = get_field( 'information_bottom_section' );
		if ( empty( $information_bottom_section['price_text'] ) && empty( $information_bottom_section['price'] ) ) {
			return '';
		}

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

	public function get_course_is_for_image() {
		$course_is_for_image = get_field( 'course_is_for_image' );

		return $course_is_for_image;
	}

	public function has_how_it_works() {
		return (
			! empty( $this->get_how_it_works_title() ) &&
			! empty( $this->get_how_it_works() )
		);
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

	public function get_how_it_works_image() {
		$how_it_works_image = get_field( 'how_it_works_image' );

		return $how_it_works_image;
	}

	public function get_feeling_images() {
		$feeling_images = get_field( 'feeling_images' );

		return $feeling_images;
	}

	public function get_course_plan_title() {
		$course_plan_title = get_field( 'course_plan_title' );

		return $course_plan_title;
	}

	public function get_course_plan_classes() {
		$course_plan_classes = get_field( 'course_plan_classes' );

		return $course_plan_classes;
	}

	public function get_course_plan_registration_link() {
		$course_plan_steps_registration = get_field( 'course_plan_classes_registration_link' );

		return $course_plan_steps_registration;
	}

	public function get_promotions() {
		$promotions = get_field( 'promotion' );

		return $promotions;
	}

	public function get_coupon_promotions() {
		$coupon_promotions = get_field( 'coupon_promotions' );

		return $coupon_promotions;
	}

	public function get_recommendations_title() {
		$recommendations_title = get_field( 'recommending_customers_title' );

		return $recommendations_title;
	}

	public function get_recommendations() {
		$recommendations_steps = get_field( 'recommending_customers_recommendations' );

		return $recommendations_steps;
	}

	public function get_legal_text() {
		$legal_text = get_field( 'legal' );

		return $legal_text;
	}

	public function get_legal_registration_link() {
		$legal_registration_link = get_field( 'legal_registration_link' );

		return $legal_registration_link;
	}

	public function get_newsletter_title() {
		$newsletter_title = get_field( 'newsletter_title' );

		return $newsletter_title;
	}

	public function get_newsletter_checkbox_text() {
		$newsletter_checkbox_text = get_field( 'newsletter_checkbox_text' );

		return $newsletter_checkbox_text;
	}
}
