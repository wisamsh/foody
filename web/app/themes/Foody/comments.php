<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foody
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}

$foody_comments = new Foody_Comments();

?>

<div id="comments" class="comments-area">

    <?php
    // You can start editing here -- including this comment!
    if (have_comments()) :
        ?>
        <h2 class="comments-title">
            <?php

            $foody_comments->the_title();

            ?>
        </h2><!-- .comments-title -->

        <?php

        $foody_comments->the_comments_form();

        ?>


        <ol class="comment-list">
            <?php
            $foody_comments->list_comments();
            ?>
        </ol><!-- .comment-list -->


        <?php
        $cpage = get_query_var('cpage') ? get_query_var('cpage') : 1;


        if ($cpage > 1) {

            ?>
            <a class="load-more" id="load-more">
                <?php
                echo __('הצג עוד', 'Foody');
                ?>
                <i class="icon-show-more-arrow"></i>
            </a>

            <?php

            echo '
                <script>
                let ajaxurl = \'' . site_url('wp-admin/admin-ajax.php') . '\',
                    parent_post_id = ' . get_the_ID() . ',
                        cpage = ' . $cpage . '
                </script>';
        }

        ?>

        <?php

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'foody'); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().

    ?>

</div><!-- #comments -->
