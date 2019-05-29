<?php
/**
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 2019-05-15
 * Time: 10:21
 */

/**
 * Add special how I did type to comments DDL
 *
 * @param $types
 *
 * @return mixed
 */
function foody_admin_comment_types_dropdown( $types ) {
	$types['how_i_did'] = __( 'תראו מה יצא לי', 'foody' );

	return $types;
}

add_filter( 'admin_comment_types_dropdown', 'foody_admin_comment_types_dropdown' );

/**
 * Add type column to comments table
 *
 * @param $cols
 *
 * @return array
 */
function foody_add_comments_columns( $cols ) {

	$foody_columns = array(
		'foody_comment_type'  => 'סוג',
		'foody_comment_image' => 'תמונה'
	);

	$cols = array_slice( $cols, 0, 3, true ) + $foody_columns + array_slice( $cols, 3, null, true );

	return $cols;
}

add_filter( 'manage_edit-comments_columns', 'foody_add_comments_columns' );

/**
 * Add type value to comments table
 *
 * @param $column
 * @param $comment_ID
 */
function foody_add_comment_columns_content( $column, $comment_ID ) {
	global $comment;
	switch ( $column ) {
		case 'foody_comment_type' :
			switch ( $comment->comment_type ) {
				case 'how_i_did':
					echo 'מה יצא לי';
					break;
				case '':
				case 'comment':
					echo 'תגובה';
					break;
				case 'pings':
					echo 'פינג';
					break;
			}
			break;
		case 'foody_comment_image':
			if ( $comment->comment_type == 'how_i_did' ) {
				$attachment_id = get_comment_meta( $comment->comment_ID, 'attachment', true );
				$image         = wp_get_attachment_url( $attachment_id );

				echo '<a href="' . $image . '" target="_blank"><img width="130px" src="' . $image . '"/></a>';
			}
			break;
	}
}

add_action( 'manage_comments_custom_column', 'foody_add_comment_columns_content', 10, 2 );

function foody_comment_how_i_did_image( $comment ) {
	if ( $comment->comment_type == 'how_i_did' ) {
		add_meta_box( 'title', __( 'תראו מה יצא לי', 'foody' ), 'foody_comment_how_i_did_image_cb', 'comment', 'normal', 'high' );
	}
}

add_action( 'add_meta_boxes_comment', 'foody_comment_how_i_did_image', 10, 1 );

function foody_comment_how_i_did_image_cb( $comment ) {

	$attachment_id = get_comment_meta( $comment->comment_ID, 'attachment', true );

	$image = wp_get_attachment_url( $attachment_id );
	?>
    <p>
    <a href="<?php echo $image ?>" target="_blank">
        <img src="<?php echo $image ?>"/>
    </a>
    </p><?php

}

function foody_approve_comment_how_i_did( $new_status, $old_status, $comment ) {
	if ( $old_status != $new_status ) {
		if ( $new_status == 'approved' ) {
			if ( $comment->comment_type == 'how_i_did' ) {
				$registration_page = get_page_by_title( 'הרשמה' );
				$campaign_url      = get_field( 'campaign_link', $registration_page );
				$campaign_page     = url_to_postid( $campaign_url['url'] );
				if ( $comment->comment_post_ID == $campaign_page ) {
					// Send approval email
					$approve_email_template = get_field( 'image_approval_email_template', $campaign_page );
					$approve_email_subject  = get_field( 'image_approval_email_subject', $campaign_page );
					Foody_Mailer::sendEmailTemplate( $approve_email_subject, $approve_email_template, $comment->comment_author_email );
				}
			}
		}
	}
}

add_action( 'transition_comment_status', 'foody_approve_comment_how_i_did', 10, 3 );
