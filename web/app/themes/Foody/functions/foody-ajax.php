<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/3/18
 * Time: 1:58 PM
 */


function foody_ajax_error( $message = 'Error' ) {

	return $message;
}

function foody_form_validation( $required ) {
	$messages = null;
	if ( ! empty( $required ) && is_array( $required ) ) {
		$messages = [];
		foreach ( $required as $item ) {
			if ( ! isset( $_POST[ $item ] ) ) {
				$messages[ $item ] = 'required';
			}
		}
	}

	return $messages;
}


require_once get_template_directory() . '/functions/ajax/comments.php';
require_once get_template_directory() . '/functions/ajax/favorites.php';
require_once get_template_directory() . '/functions/ajax/follow.php';
require_once get_template_directory() . '/functions/ajax/search.php';
require_once get_template_directory() . '/functions/ajax/load-more.php';
require_once get_template_directory() . '/functions/ajax/edit-user.php';
require_once get_template_directory() . '/functions/ajax/page-load.php';
require_once get_template_directory() . '/functions/ajax/bit-pay-manager.php';
//require_once get_template_directory() . '/functions/ajax/duplicate-titles.php';

function foody_parse_checkbox( $key ) {
	$checked = 0;

	if ( isset( $_POST[ $key ] ) ) {
		$value = $_POST[ $key ];
		if ( $value === 'on' ) {
			$checked = 1;
		} elseif ( $value === true || $value == "1" ) {
			$checked = 1;
		}
	}

	return $checked;
}