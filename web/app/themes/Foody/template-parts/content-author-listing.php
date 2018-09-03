<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/28/18
 * Time: 8:12 PM
 */

$order = isset($template_args['order']) ? $template_args['order'] : 0;

?>
<a class="author col" href="<?php echo get_author_posts_url($template_args['id']) ?>">
    <div data-name="<?php echo $template_args['name'] ?>" data-order="<?php echo $order ?>">
        <div class="image-container">
            <img class="avatar" src="<?php echo $template_args['image'] ?>" alt="">

            <?php if (isset($template_args['post_count'])): ?>
                <span class="post-count">
        <?php echo $template_args['post_count'] ?>
        </span>

            <?php endif; ?>
        </div>


        <h4 class="author-name"> <?php echo $template_args['name'] ?> </h4>

    </div>
</a>