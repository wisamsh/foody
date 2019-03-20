<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/2/18
 * Time: 2:27 PM
 */
?>

<!--<div>-->
<!--    <form class="foody-comment-form" id="commentform" action="--><?php //echo get_option('siteurl'); ?><!--/wp-comments-post.php"-->
<!--          method="post"-->
<!--          id="commentform">-->
<div class="commentform-element<?php echo (is_user_logged_in() ? ' logged-in-comment' : ' anonymous-comment') ?>">
	<?php echo get_avatar( get_current_user_id(), 52 ) ?>
	<?php if ( !is_user_logged_in() ): ?>
        <input autocomplete="off" id="author-name" class="input-fields" placeholder="שם מלא" name="author"
        />
        <input autocomplete="off" id="author-email" class="input-fields" placeholder="כתובת מייל" name="email"
        />
	<?php endif; ?>
    <input autocomplete="off" id="comment" class="input-fields" placeholder="הוסף תגובה" name="comment"
    />
</div>
<!--        <input name="submit" class="form-submit-button" type="submit" id="submit-comment" value="Post comment">-->
<!--        <input type="hidden" name="comment_post_ID" value="--><?php //echo get_the_ID() ?><!--" id="comment_post_ID">-->
<!--        <input type="hidden" name="comment_parent" id="comment_parent" value="0">-->
<!--    </form>-->
<!--</div>-->



