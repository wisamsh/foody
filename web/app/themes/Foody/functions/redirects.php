<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/26/18
 * Time: 12:03 PM
 */

//function wpb_change_search_url()
//{
//    if (is_search() && !empty($_GET['s'])) {
//        wp_redirect(home_url("/search/") . urlencode(get_query_var('s')));
//        exit();
//    }
//}
//
//add_action('template_redirect', 'wpb_change_search_url');


/*
*   Restrict non logged users to certain pages
*/

add_action( 'template_redirect', 'foody_non_logged_redirect' );
function foody_non_logged_redirect() {
	$restricted_pages = array(
		'פרופיל-אישי'
	);

	$slug = urldecode( get_post_field( 'post_name', get_post() ) );

	if ( in_array( $slug, $restricted_pages ) && ! is_user_logged_in() ) {
		wp_redirect( wp_login_url( get_permalink() ) );
		die();
	}
}


/*
*   Restrict logged users from login and registration
*/

add_action( 'template_redirect', 'foody_logged_redirect' );
function foody_logged_redirect() {
	$signon_pages = array(
		'הרשמה',
		'התחברות'
	);

	$slug = urldecode( get_post_field( 'post_name', get_post() ) );

//	if ( (in_array( $slug, $signon_pages ) || is_home()) && is_user_logged_in()) {
    if ( in_array( $slug, $signon_pages ) && is_user_logged_in()) {
        if ( ! isset( $_REQUEST['registered']) ) {
//            if( isset($_GET['camping']) ) {
               // wp_redirect( home_url() );
                wp_redirect(get_permalink(get_page_by_path('השלמת-רישום')));
                die();
//            }
//            else {
//                wp_redirect( home_url() );
//                die();
//            }
//
		}
	}
}