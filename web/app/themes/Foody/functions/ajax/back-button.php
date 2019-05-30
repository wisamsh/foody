<?php
/**
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 2019-05-30
 * Time: 10:58
 */


add_action( 'wp_ajax_foody_back_button', 'foody_update_back_button' );
add_action( 'wp_ajax_nopriv_foody_back_button', 'foody_update_back_button' );

function foody_update_back_button() {
	$stuff                   = $_POST;
	$_SESSION['back_button'] = $_POST['back_button'];
	die();
}