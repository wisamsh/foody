<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/3/19
 * Time: 3:31 PM
 */

add_filter( 'foody_js_messages', 'foody_add_registration_messages' );
function foody_add_registration_messages( $messages ) {

	$registration_page = get_page_by_title( 'הרשמה' );
	$error_message     = get_field( 'error_text', $registration_page );
	if ( empty( $error_message ) ) {
		$error_message = __( 'נשמח לשלוח לך את ספר המתכונים, אבל קודם יש לאשר קבלת דואר מאתר Foody' );
	}

	if ( ! isset( $messages['registration'] ) || ! is_array( $messages['registration'] ) ) {
		$messages['registration'] = [];
	}

	$messages['registration']['eBookError'] = $error_message;

	return $messages;
}

