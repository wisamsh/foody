<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 4:49 PM
 */
class Foody_HowIDid {


	/**
	 * Foody_HowIDid constructor.
	 */
	public function __construct() {
	}


	public function get_comments( $additional_args = [] ) {
		$additional_args = array_merge( $additional_args, $this->get_args() );

		return get_comments( $additional_args );
	}


	public function get_args() {
		$comments_per_page = get_field( 'how_i_did_paging' );
		if ( empty( $comments_per_page ) ) {
			$comments_per_page = get_option( 'hid_per_page' );
		}
		$args =
			array(
				'type'    => 'how_i_did',
				'number'  => 3,
				'walker'  => new Foody_HowIDidWalker(),
				'post_id' => get_the_ID(),
			);

		$args = array(
			'type__not_in' => array( 'comment', 'pings' ),
			'type'         => 'how_i_did',
			'number'       => $comments_per_page,
			'post_id'      => get_the_ID(),
			'orderby'      => 'comment_date_gmt',
		);

		return $args;
	}

	/**
	 * @param array $args
	 */
	public function the_comments( $args = [] ) {
		$args = array_merge( $args, $this->get_args() );

		$comments = get_comments( $args );

		$filtered_comments = array_filter( $comments, function ( $comment ) {
			return $this->filter_comments( $comment );
		} );

		foreach ( $filtered_comments as $comment ) {
			foody_get_template_part( get_template_directory() . '/template-parts/content-comment-how-i-did.php', $comment );
		}
	}


	public function the_upload_popup() {
		foody_get_template_part( get_template_directory() . '/template-parts/content-how-i-did-popup.php' );
	}

	/**
	 * @param bool $echo
	 *
	 * @return string|null
	 */
	public function the_title( $echo = true ) {
		$comments = get_comments( array( 'type' => 'how_i_did', 'post_id' => get_the_ID() ) );

		$filtered_comments = array_filter( $comments, function ( $comment ) {
			return $this->filter_comments( $comment );
		} );

		$foody_comment_count = count( $filtered_comments );

		$how_i_did_title = get_field( 'how_i_did_title' );
		if ( empty( $how_i_did_title ) ) {
			$how_i_did_title = 'תראו מה יצא לי';
		}

		$title = sprintf(
		/* translators: 1: comment count number, 2: title. */
			esc_html( _nx( $how_i_did_title . ' (%s)', $how_i_did_title . ' (%s)', $foody_comment_count, 'comments title', 'foody' ) ),
			number_format_i18n( $foody_comment_count )
		);

		if ( $echo ) {
			echo $title;
		}

		return $title;
	}

	function get_the_title() {
		return $this->the_title( false );
	}

	public function the_comments_form() {
	}

	public function get_page_count() {

		$args = $this->get_args();
		unset( $args['number'] );
		$comments = get_comments( $args );

		$comments_per_page = get_field( 'how_i_did_paging' );
		if ( empty( $comments_per_page ) ) {
			$comments_per_page = get_option( 'hid_per_page', 3 );
		}
		$num_of_pages = get_comment_pages_count( $comments, $comments_per_page );

		return $num_of_pages;

	}

	function filter_comments( $comment ) {
		$author = get_user_by( 'email', $comment->comment_author_email );

		return $comment->comment_approved || ( ! $comment->comment_approved && $author->ID == get_current_user_id() );
	}

}