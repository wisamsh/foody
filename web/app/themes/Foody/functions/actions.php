<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/4/18
 * Time: 4:32 PM
 */

/**
 * @param $post_id
 *
 * @return bool
 */
function is_comments_open( $post_id ) {
	$comments_closed      = get_option( 'close_comments_for_old_posts' );
	$comments_closed_days = get_option( 'close_comments_days_old' );
	$post_comments_open   = comments_open( $post_id );

	$open = $post_comments_open;

	if ( ! foody_is_registration_open() ) {
		$open = false;
	} else if ( ( isset( $comments_closed ) && $comments_closed )
        && ( isset( $comments_closed_days ) && $comments_closed_days == 0 ) ) {
		$open = false;
	}

	return $open;
}