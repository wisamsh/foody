<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/28/18
 * Time: 8:12 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$order = isset( $template_args['order'] ) ? $template_args['order'] : 0;

?>
<a class="author col" href="<?php echo get_author_posts_url( $template_args['id'] ) ?>">
    <div data-name="<?php echo $template_args['name'] ?>" data-order="<?php echo $order ?>">
        <div class="image-container">
            <img class="avatar lazyload" src="<?php echo $GLOBALS['images_dir'] . 'author-placeholder.svg' ?>"
                 data-foody-src="<?php echo $template_args['image'] ?>" alt="<?php echo $template_args['name'] ?>">

			<?php if ( isset( $template_args['post_count'] ) ): ?>
                <span class="post-count">
        <?php echo $template_args['post_count'] ?>
        </span>

			<?php endif; ?>
        </div>


        <h2 class="author-name"> <?php echo $template_args['name'] ?> </h2>

    </div>
</a>