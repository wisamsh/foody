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

$author = get_user_by('email', $comment['comment_author_email']);
?>


<div class="col-sm-4 col-6 how-i-did">
    <div class="image-container">
        <a class="how-i-did-modal-open" href="#how-i-did-modal" data-toggle="modal" data-image="<?php echo $image ?>"
           data-user="<?php echo $author->display_name ?>"
           data-content="<?php echo strip_tags(get_comment_text($comment['comment_ID'])); ?>">
            <img src="<?php echo $image ?>" alt="">
        </a>
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
                <?php echo human_time_diff(get_comment_date('U', $comment['comment_ID']), date('U')) ?>
            </time>
            <?php comment_text($comment['comment_ID']); ?>
        </div>
    </div>
</div>
