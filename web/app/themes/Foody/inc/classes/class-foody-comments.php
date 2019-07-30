<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/4/18
 * Time: 7:16 PM
 */
class Foody_Comments {


	/**
	 * Foody_Comments constructor.
	 */
	public function __construct() {
	}


	public function list_comments( $args = null ) {
		if ( $args == null ) {
			$args = $this->get_list_comments_args();
		}

//        $comments = get_comments(array('type' => 'comment', 'post_id' => get_the_ID()));

		wp_list_comments( $args );
	}


	public function get_list_comments_args() {
		return array(
			'style'      => 'ul',
			'format'     => 'html5',
			'short_ping' => true,
			'type'       => 'comment',
			'walker'     => new Foody_CommentWalker(),
			'per_page'   => get_option( 'comments_per_page' ),
			'max_depth'  => 2,
			'reply_text' => __( 'הוסף תגובה', 'Foody' )
		);
	}

	public function the_comments_form() {
		comment_form(
			array(
				'logged_in_as'         => '',
				'comment_field'        => foody_get_template_part(
					get_template_directory() . '/template-parts/comment_form.php',
					array( 'return' => true )
				),
				'fields'               => [],
				'title_reply'          => '',
				'comment_notes_before' => '',
				'label_submit'         => __( 'שלח', 'WordPress' ),
			)
		);
	}

	public function the_title( $echo = true ) {
		$foody_comment_count = get_comments( array( 'status'  => 'approve',
		                                            'count'   => true,
		                                            'type'    => 'comment',
		                                            'post_id' => get_the_ID()
		) );

		$title = sprintf(
		/* translators: 1: comment count number, 2: title. */
			esc_html( _nx( 'תגובה אחת', 'תגובות (%s)', $foody_comment_count, 'comments title', 'foody' ) ),
			number_format_i18n( $foody_comment_count )
		);

		if ( $echo ) {
			echo $title;
		}

		return $title;
	}

	public function get_the_title() {
		return $this->the_title( false );
	}
}