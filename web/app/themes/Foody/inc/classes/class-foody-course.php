<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/6/18
 * Time: 8:35 PM
 */
class Foody_Course {

	public $show_how_i_did = false;

	/**
	 * Course constructor.
	 */
	public function __construct($post) {
		$this->show_how_i_did = get_field( 'show_how_i_did' );
	}

	public function how_i_did() {
		$template = '/comments-how-i-did.php';

		if ( wp_is_mobile() ) {
			$template = '/comments-how-i-did-mobile.php';
		}

		comments_template(
			$template
		);
	}

}