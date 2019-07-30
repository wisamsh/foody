<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/27/18
 * Time: 3:36 PM
 */

$action = get_query_var( 'registered', null );

$template = 'register-form.php';
$args     = isset( $template_args ) && isset( $template_args['text'] ) ? [ 'text' => $template_args['text'] ] : [];
if ( ! is_null( $action ) && is_user_logged_in() ) {

	$user_id = get_current_user_id();

	$user     = get_user_by( 'ID', $user_id );
	$template = 'login-welcome.php';
	$args     = array_merge( $args, [
		'profile'   => [
			'username' => $user->display_name
		],
		'marketing' => get_user_meta( $user_id, 'marketing', true ),
		'e-book'    => get_user_meta( $user_id, 'e_book', true )
	] );
}

foody_get_template_part( get_template_directory() . "/template-parts/$template", $args );