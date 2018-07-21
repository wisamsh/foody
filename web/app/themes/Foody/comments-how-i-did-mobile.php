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

$foody_comments = new Foody_HowIDid();

?>

<div id="comments how-i-did" class="comments-area">

    <?php

    $have_comments = get_comments(array(
            'type' => 'how_i_did',
            'count' => true
        )) > 0;
    // You can start editing here -- including this comment!
    if ($have_comments) :
        ?>

        <?php
        $accordion_args = array(
            'id' => 'how-i-did',
            'title_classes' => 'how-i-did-title',
            'title' => $foody_comments->get_the_title(),
            'content' => function () use ($foody_comments) {

                ?>

                <div class="header">

                    <div class="add-image">
                        <form id="image-upload-hidden">
                            <label for="attachment">
                                <i class="icon-camera"></i>
                                <span>
                             תעלו תמונה להשוויץ
                        </span>
                            </label>
                            <input id="attachment" type="file" name="attachment">
                            <input id="comment" type="hidden" name="comment">
                            <input name="post_id" type="hidden" value="<?php echo get_the_ID() ?>">
                        </form>
                    </div>
                </div>

                <ol id="how-i-did-list" class="row how-i-did-list">
                    <?php
                    $foody_comments->the_comments();
                    ?>
                </ol><!-- .comment-list -->


                <?php

                foody_get_template_part(
                    get_template_directory() . '/template-parts/common/show-more-simple.php',
                    array(
                        'context' => 'how-i-did-list'
                    ));
            },
            'classes' => 'accordion-mobile'
        );

        foody_get_template_part(get_template_directory() . '/template-parts/common/accordion.php', $accordion_args);
        ?>


        <?php
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-image-upload-modal.php',
            array(
                'title' => 'תיראו מה יצא לי'
            ));
        ?>


        <?php

    endif;


    ?>

</div><!-- #comments -->
