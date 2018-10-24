<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/2/18
 * Time: 11:53 AM
 */

$tags = isset($template_args) ? $template_args : wp_get_post_tags();

?>


<ul class="post-tags">

    <?php foreach ($tags as $tag): ?>

        <li class="tag">

            <a href="<?php echo get_term_link($tag->term_id) ?>">
                <?php echo $tag->name ?>
            </a>
        </li>

    <?php endforeach; ?>

</ul>
