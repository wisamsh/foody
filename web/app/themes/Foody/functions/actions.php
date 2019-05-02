<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/4/18
 * Time: 4:32 PM
 */

add_action( 'pre_get_comments', function ( \WP_Comment_Query $query ) {
	/* only allow 'my_custom_comment_type' when is required explicitly */
	if ( $query->query_vars['type'] !== 'how_i_did' ) {
		$query->query_vars['type__not_in'] = array_merge(
			(array) $query->query_vars['type__not_in'],
			array( 'how_i_did' )
		);
	}
} );


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

	if ( ( isset( $comments_closed ) && $comments_closed )
	     && ( isset( $comments_closed_days ) && $comments_closed_days == 0 ) ) {
		$open = false;
	}

	return $open;
}