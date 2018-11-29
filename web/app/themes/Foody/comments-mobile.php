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

    $have_comments = get_comments(array(
            'type' => 'comment',
            'count' => true
        )) > 0;
    // You can start editing here -- including this comment!
//    if ($have_comments) :
        ?>

        <?php

        $accordion_args = array(
            'id' => 'comments-accordion',
            'title' => $foody_comments->get_the_title(),
            'content' => function () use ($foody_comments) {
                $foody_comments->the_comments_form();

                ?>

                <ol id="comments-list" class="comment-list">
                    <?php
                    $foody_comments->list_comments();
                    ?>
                </ol><!-- .comment-list -->


                <?php

                $cpage = get_query_var('cpage') ? get_query_var('cpage') : 1;

                if ($cpage > 1) {

                    foody_get_template_part(
                        get_template_directory() . '/template-parts/common/show-more-simple.php',
                        array(
                            'context' => 'comments-list'
                        )
                    );

                    echo '
                <script>
                let ajaxurl = \'' . site_url('wp-admin/admin-ajax.php') . '\',
                    parent_post_id = ' . get_the_ID() . ',
                        cpage = ' . $cpage . '
                </script>';
                }

                // If comments are closed and there are comments, let's leave a little note, shall we?
                if (!comments_open()) :
                    ?>
                    <p class="no-comments"><?php esc_html_e('Comments are closed.', 'foody'); ?></p>
                    <?php
                endif;

            },
            'classes' => 'accordion-mobile'
        );

        foody_get_template_part(get_template_directory() . '/template-parts/common/accordion.php', $accordion_args);

//    endif; // Check for have_comments().

    ?>

</div><!-- #comments -->
