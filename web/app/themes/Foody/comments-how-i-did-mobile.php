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

$foody_how_i_did = new Foody_HowIDid();

?>

<div id="how-i-did" class="comments-area">

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
            'id' => 'how-i-did-accordion',
            'title_classes' => 'how-i-did-title comments-title',
            'title' => $foody_how_i_did->get_the_title(),
            'content' => function () use ($foody_how_i_did) {

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
                            <input id="attachment" type="file" accept="image/*" name="attachment">
                            <input id="comment" type="hidden" name="comment">
                            <input name="post_id" type="hidden" value="<?php echo get_the_ID() ?>">
                        </form>
                    </div>
                </div>

                <?php $foody_how_i_did->the_upload_popup(); ?>

                <ol id="how-i-did-list" class="row how-i-did-list">
                    <?php
                    $foody_how_i_did->the_comments();
                    ?>
                </ol><!-- .comment-list -->


                <?php

                $page = get_query_var('hid_page', null);

                if ($page == null) {
                    $page = $foody_how_i_did->get_page_count();
                    set_query_var('hid_page', $page);
                }

                if ($page > 1) {

                    foody_get_template_part(
                        get_template_directory() . '/template-parts/common/show-more-simple.php',
                        array(
                            'context' => 'how-i-did-list'
                        ));

                    echo '
                <script>
                if(!ajaxurl){
                    var ajaxurl = \'' . site_url('wp-admin/admin-ajax.php') . '\';
                    var parent_post_id = ' . get_the_ID() . '
                }
                let hidpage = ' . $page . '
                </script>';
                }
            },
            'classes' => 'accordion-mobile'
        );

        foody_get_template_part(get_template_directory() . '/template-parts/common/accordion.php', $accordion_args);
        ?>


        <?php
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-image-upload-modal.php',
            array(
                'title' => 'תראו מה יצא לי'
            ));
        ?>


        <?php

    endif;


    ?>

</div><!-- #comments -->
