<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/24/18
 * Time: 12:06 PM
 */


function foody_edit_user()
{
    $required = [
        'first_name',
        'last_name'
    ];

    $validation_errors = foody_form_validation($required);


    $errors = new WP_Error();


    if (!is_user_logged_in()) {
        $errors->add(401, 'unauthorized');
    } else {
        if (!empty($validation_errors)) {
            $errors->add(400, 'bad request');
        } else {

            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];

            $user_data = new stdClass();

            $user_data->first_name = $first_name;
            $user_data->last_name = $last_name;

            $user_data->ID = get_current_user_id();


            $updated_user_id = wp_update_user($user_data);


            if (is_wp_error($updated_user_id)) {

                $errors->add(500, 'error updating user');

			} else {
				if ( isset( $_POST['phone_number'] ) ) {
					$result = update_user_meta( $updated_user_id, 'phone_number', $_POST['phone_number'] );
					if ( ! $result ) {
						$errors->add( 500, 'error updating phone number' );
					}
				}
			}

		}
	}

	if ( ! empty( $errors->errors ) ) {
		wp_send_json_error( $errors );
	} else {
		wp_send_json_success( get_user_by( 'ID', get_current_user_id() ) );
	}


}

add_action( 'wp_ajax_foody_edit_user', 'foody_edit_user' );


function foody_edit_profile_picture() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( [ 'message' => 'Not Authorized' ], 401 );
	}

	$id = get_current_user_id();

	$avatar = wp_handle_upload( $_FILES['photo'], array(
		'test_form'                => false,
		'unique_filename_callback' => 'wp_user_avatars_unique_filename_callback'
	) );

	if ( empty( $avatar ) ) {
		wp_send_json_error( [ 'message' => 'Upload Failed' ], 500 );
	}

	if ( function_exists( 'wp_user_avatars_update_avatar' ) ) {
		wp_user_avatars_update_avatar( $id, $avatar['url'] );
		wp_send_json_success( $avatar );
	} else {
		wp_send_json_error( [ 'message' => 'User Avatars is Disabled' ], 502 );
	}

}

add_action( 'wp_ajax_foody_edit_profile_picture', 'foody_edit_profile_picture' );


function foody_edit_user_approvals() {

	$errors = new WP_Error();

	$marketing = isset( $_POST['marketing'] ) ? $_POST['marketing'] : false;
	$e_book    = isset( $_POST['e_book'] ) ? $_POST['e_book'] : false;


	$ID = get_current_user_id();

	$user = get_user_by( 'ID', $ID );

	if ( ! empty( $marketing ) ) {
		$resultMarketing = update_user_meta( $ID, 'marketing', $marketing );
		if ( ! empty( $user ) && $user->ID != 0 ) {
			foody_register_newsletter( $user->user_email );
		}
	}

	if ( empty( $marketing ) ) {
		$marketing = get_user_meta( $ID, 'marketing', true );
	}

	if ( $marketing && ! empty( $user ) && $user->ID != 0 ) {
		foody_register_newsletter( $user->user_email );
	}

	$resultMarketingEbook = update_user_meta( $ID, 'e_book', $e_book );
	update_user_meta( $ID, 'seen_approvals', true );


	if (
		( isset( $resultMarketing ) &&
		  $resultMarketing === false ) ||
		$resultMarketingEbook === false
	) {
		$errors->add( 500, 'error updating user' );
	}


	if ( ! empty( $errors->errors ) ) {
		wp_send_json_error( $errors, 400 );
	} else {
		$user = get_user_by( 'ID', get_current_user_id() );
		if ( $e_book === "true" || $e_book === true ) {
			$action             = isset( $_GET ) && isset( $_GET['success'] ) ? $_GET['success'] : null;
			$registration_page  = get_page_by_title( 'הרשמה' );
			$campaign_url       = get_field( 'campaign_link', $registration_page );
			$campaign_page      = url_to_postid( $campaign_url['url'] );
			$campaign_thank_you = get_field( 'thank_you_campaign', $campaign_page );
			//Send email
			$subject = get_field( 'campaign_mail_subject', $registration_page );
			Foody_Mailer::send( $subject, 'e-book', $user->user_email );
			wp_send_json_success( [ 'ebook' => $e_book, 'go-to' => $campaign_thank_you ] );
		} else {
			wp_send_json_success( [ 'ebook' => $e_book ] );
		}
	}
}

add_action( 'wp_ajax_foody_edit_user_approvals', 'foody_edit_user_approvals' );


function foody_edit_user_approvals_viewed() {

	$errors = new WP_Error();


	$seen_approvals = isset( $_POST['seen_approvals'] ) ? $_POST['seen_approvals'] : false;
	$ID             = get_current_user_id();

	$resultSeen = update_user_meta( $ID, 'seen_approvals', $seen_approvals );

	if ( $resultSeen === false ) {
		$errors->add( 500, 'error updating user' );
	}

	if ( ! empty( $errors->errors ) ) {
		wp_send_json_error( $errors );
	} else {
		$user = get_user_by( 'ID', get_current_user_id() );
		wp_send_json_success( $user );
	}
}

add_action( 'wp_ajax_foody_edit_user_approvals_viewed', 'foody_edit_user_approvals_viewed' );


function foody_edit_user_extended_campaign_approvals() {

	$errors = new WP_Error();

	$street                  = isset( $_POST['street'] ) ? $_POST['street'] : '';
	$street_number           = isset( $_POST['street-number'] ) ? $_POST['street-number'] : '';
	$city                    = isset( $_POST['city'] ) ? $_POST['city'] : '';
	$birthday                = isset( $_POST['birthday'] ) ? $_POST['birthday'] : '';
	$gender                  = isset( $_POST['gender'] ) ? $_POST['gender'] : '';
	$extended_campaign_terms = isset( $_POST['extended-campaign-terms'] ) ? $_POST['extended-campaign-terms'] : false;
	$marketing               = isset( $_POST['marketing'] ) ? $_POST['marketing'] : false;


	$ID = get_current_user_id();

	$user = get_user_by( 'ID', $ID );


	if ( ! empty( $marketing ) ) {
		$resultMarketing = update_user_meta( $ID, 'marketing', $marketing );
		if ( ! empty( $user ) && $user->ID != 0 ) {
			foody_register_newsletter( $user->user_email );
		}
	}

	if ( empty( $marketing ) ) {
		$marketing = get_user_meta( $ID, 'marketing', true );
	}

	if ( $marketing && ! empty( $user ) && $user->ID != 0 ) {
		foody_register_newsletter( $user->user_email );
	}

	if ( ! empty( $street ) ) {
		$resultStreet = update_user_meta( $ID, 'street', $street );
	}

	if ( ! empty( $street_number ) ) {
		$resultStreetNumber = update_user_meta( $ID, 'street_number', $street_number );
	}

	if ( ! empty( $city ) ) {
		$resultCity = update_user_meta( $ID, 'city', $city );
	}

	if ( ! empty( $birthday ) ) {
		$resultBirthday = update_user_meta( $ID, 'birthday', $birthday );
	}

	if ( ! empty( $gender ) ) {
		$resultGender = update_user_meta( $ID, 'gender', $gender );
	}

	$resultCampaignTerms = update_user_meta( $ID, 'extended-campaign-terms', $extended_campaign_terms );

	update_user_meta( $ID, 'seen_extended_approvals', true );


	if (
		( isset( $resultStreet ) &&
		  $resultStreet === false ) ||
		( isset( $resultStreetNumber ) &&
		  $resultStreetNumber === false ) ||
		( isset( $resultCity ) &&
		  $resultCity === false ) ||
		( isset( $resultBirthday ) &&
		  $resultBirthday === false ) ||
		( isset( $resultGender ) &&
		  $resultGender === false ) ||
		$resultCampaignTerms === false
	) {
		$errors->add( 500, 'error updating user' );
	}


	if ( ! empty( $errors->errors ) ) {
		wp_send_json_error( $errors, 400 );
	} else {
		$user = get_user_by( 'ID', get_current_user_id() );
		wp_send_json_success( [ 'result' => true ] );
	}
}

add_action( 'wp_ajax_foody_edit_user_extended_campaign_approvals', 'foody_edit_user_extended_campaign_approvals' );