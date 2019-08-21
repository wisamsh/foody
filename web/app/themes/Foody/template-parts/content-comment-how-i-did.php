<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 6:30 PM
 */


/** @var WP_Comment $comment */
/** @noinspection PhpUndefinedVariableInspection */
$comment = $template_args;

$attachment_id = get_comment_meta( $comment['comment_ID'], 'attachment', true );

$image = wp_get_attachment_url( $attachment_id );

$author   = get_user_by( 'email', $comment['comment_author_email'] );
$username = $author->display_name;

$comments_per_page = get_option( 'hid_per_page', 2 );

$col_class = '';
$cols      = 12;

if ( $cols % $comments_per_page == 0 ) {
	$col       = $cols / $comments_per_page;
	$col_class = "col-sm-$col";
} else {
	$col_class = 'col';
}
?>


<div class="col-sm-4 <?php echo $col_class ?> how-i-did">
    <div class="image-container">
		<?php if ( $comment['comment_approved'] ) : ?>
            <!--suppress HtmlUnknownAnchorTarget -->
            <a class="how-i-did-modal-open" href="#how-i-did-modal" data-toggle="modal"
               data-image="<?php echo $image ?>"
               data-user="<?php echo $author->display_name; ?>"
               data-content="<?php echo strip_tags( get_comment_text( $comment['comment_ID'] ) ); ?>">
                <img src="<?php echo $image ?>"
                     alt="<?php echo strip_tags( get_comment_text( $comment['comment_ID'] ) ); ?>">
            </a>
		<?php else: ?>
            <div class="waiting-for-approval">
                <img src="<?php echo $GLOBALS['images_dir'] . 'how-i-did-waiting-mobile.png' ?>"
                     alt="<?php echo __( 'ממתין לאישור' ) ?>">
            </div>
		<?php endif; ?>
    </div>
    <div class="author row gutter-0">
        <div>
			<?php echo get_avatar( $comment['user_id'], 54 ); ?>
        </div>
        <div class="col">
               <span class="username">
                    <?php printf( __( '%s' ), sprintf( '<span class="author-name">%s</span>', $author->first_name.' '.$author->last_name ) ); ?>
                </span>
            <time>
				<?php echo human_time_diff( get_comment_date( 'U', $comment['comment_ID'] ), date( 'U' ) ) ?>
            </time>
			<?php comment_text( $comment['comment_ID'] ); ?>
        </div>
    </div>
</div>
