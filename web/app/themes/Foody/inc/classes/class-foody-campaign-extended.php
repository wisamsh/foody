<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/6/18
 * Time: 8:35 PM
 */
class Foody_Campaign_Extended {

	public $campaign;
	public $show_how_i_did = false;

	/**
	 * E-Book constructor.
	 */
	public function __construct() {
		$this->campaign       = new Foody_Campaign();
		$this->show_how_i_did = get_field( 'show_how_i_did' );
		if ( ! Foody_User::user_has_meta( 'seen_extended_approvals' ) ) {
			$this->campaign->registered_user_link = get_field( 'extended_campaign_url' );
		}
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