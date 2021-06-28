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
    <h2 class="title"><?php echo $foody_comments->get_the_title() ?></h2>
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
                global $wp_query, $post;
                $per_page = 3;
                $cpage = ( ceil( get_comments( array(
                        'post_id' => $post->ID,
                        'type'  => 'comment',
                        'count' => true,
                        'parent' => 0
                    ) ) / $per_page ) );
                if(isset($wp_query->query_vars)){
                    $wp_query->query_vars['cpage'] = $cpage;
                }
                $foody_comments->list_comments(null);

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
                <script async defer>
                if(!ajaxurl){
                    var ajaxurl = \'' . site_url('wp-admin/admin-ajax.php') . '\';
                    var parent_post_id = ' . get_the_ID() . '
                }
                let cpage = ' . $cpage . '               
                </script>';
            }

            // If comments are closed and there are comments, let's leave a little note, shall we?
            if (!comments_open()) :
                ?>
                <p class="no-comments"><?php esc_html_e('תגובות נסגרו ע״י אדמין', 'foody'); ?></p>
            <?php
            endif;

        },
        'classes' => 'accordion-mobile'
    );

    foody_get_template_part(get_template_directory() . '/template-parts/common/accordion.php', $accordion_args);

    //    endif; // Check for have_comments().

    ?>

</div><!-- #comments -->
