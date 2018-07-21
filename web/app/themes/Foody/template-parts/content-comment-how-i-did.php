<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 6:30 PM
 */


/** @var WP_Comment $comment */
$comment = $template_args;

$attachment_id = get_comment_meta($comment['comment_ID'], 'attachment', true);

$image = wp_get_attachment_url($attachment_id);

?>


<div class="col-sm-4 col-6 how-i-did">
    <div class="image-container">
        <img src="<?php echo $image ?>" alt="">
    </div>
    <div class="author row gutter-0">
        <div>
            <?php echo get_avatar($comment['user_id'], 54); ?>
        </div>
        <div class="col">
           <span class="username">
                <?php printf(__('%s'), sprintf('<span class="author-name">%s</span>', get_comment_author_link($comment['comment_ID']))); ?>
            </span>
            <time>
                <?php echo human_time_diff(get_comment_date('U',$comment['comment_ID']), date('U')) ?>
            </time>
            <?php comment_text($comment['comment_ID']); ?>
        </div>
    </div>
</div>
