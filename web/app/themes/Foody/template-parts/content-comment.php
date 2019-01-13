<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/2/18
 * Time: 7:10 PM
 */

$args = $template_args;
/** @var WP_Comment $comment */
$comment = $args['comment'];

if (!isset($args['max_depth'])) {
    $args['max_depth'] = 1;
}

if (!isset($args['depth'])) {
    $args['depth'] = 1;
}

$author = get_comment_author_email($comment);
$user = get_user_by('email',$author);
$username = $user->display_name;

?>

<article id="div-comment-<?php comment_ID(); ?>"
         class="comment-body">

    <div class="foody-comment">

        <?php echo get_avatar($comment, 54); ?>

        <div class="comment-body" id="comment-body-<?php echo $comment->comment_ID ?>">
            <?php if (!$comment->comment_approved): ?>
                <div class="waiting-approval title">
                    <?php echo __('ממתין לאישור','foody')?>
                </div>
            <?php endif; ?>
            <?php printf(__('%s'), sprintf('<span class="author">%s</span>', $username)); ?>
            <time>
                <?php echo human_time_diff(get_comment_date('U'), date('U')) ?>
            </time>

            <?php comment_text(); ?>
        </div>

        <?php
        comment_reply_link(array_merge($args, array(
            'add_below' => 'comment-body',
            'depth' => $args['depth'],
            'max_depth' => $args['max_depth'],
            'before' => '<div class="reply">',
            'after' => '</div>'
        )));
        ?>

    </div>


</article><!-- .comment-body -->



