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
$component_data = get_field('share_execute_group');
$foody_how_i_did = new Foody_HowIDid();

$btn_text = $component_data['btn_text'];
if (empty($btn_text)) {
    $btn_text = 'כן';
}

$popup_title = $component_data['text'];
if (empty($popup_title)) {
    $popup_title = 'תראו מה יצא לי';
}

$cover_image = $component_data['image'];
if (!is_array($cover_image) || !isset($cover_image['url']) || empty($cover_image['url'])) {
    $cover_image = false;
}

$show_upload = get_field('how_i_did_hide_upload');
if (is_null($show_upload)) {
    $show_upload = true;
}

$upload_text = __('תעלו תמונה להשוויץ');


//$upload_text = get_field( 'how_i_did_upload_text' );
//if ( empty( $upload_text ) ) {
//	$upload_text = 'תעלו תמונה להשוויץ';
//}
//
//$popup_title = get_field( 'how_i_did_title' );
//if ( empty( $popup_title ) ) {
//	$popup_title = 'תראו מה יצא לי';
//}
//
//$show_upload = get_field( 'how_i_did_hide_upload' );
//if ( is_null( $show_upload ) ) {
//	$show_upload = true;
//}

?>

<div id="how-i-did" class="comments-area">
    <?php if ($cover_image !== false) {
        $alt = !empty($cover_image['alt']) ? $cover_image['alt'] : ''; ?>
        <img src="<?php echo $cover_image['url']; ?>" alt="<?php echo $cover_image['alt']; ?>" class="how-i-did-cover">
    <?php } else { ?>
        <img src="<?php echo get_the_post_thumbnail_url() ?>" alt="<?php echo esc_html ( get_the_title() ) ?>" class="how-i-did-cover" >
    <?php } ?>
    <div class="comments-area-header">
        <?php if ($show_upload): ?>
            <h3 class="title"><?php echo $popup_title; ?></h3>
        <div class="btn-container">
            <div class="btn-text"><?php echo $btn_text; ?></div>
        </div>
            <div class="add-image">
                <form id="image-upload-hidden">
                    <label for="attachment">
                        <i class="icon-camera"></i>
                        <span>
                             <?php echo $upload_text ?>
                        </span>
                    </label>
                    <input id="attachment" type="file" accept="image/*" name="attachment">
                    <input id="comment" type="hidden" name="comment">
                    <input name="post_id" type="hidden" value="<?php echo get_the_ID() ?>">
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php $foody_how_i_did->the_upload_popup(); ?>
    <?php

    $have_comments = get_comments(array(
            'type' => 'how_i_did',
            'count' => true,
            'post_id' => $post->ID
        )) > 0;
    // You can start editing here -- including this comment!
    if ($have_comments) :
        ?>

        <?php
        $accordion_args = array(
            'id' => 'how-i-did-accordion',
            'title_classes' => 'how-i-did-title comments-title',
            'title' => $foody_how_i_did->get_the_title(),
            'content' => function () use ($foody_how_i_did, $upload_text, $show_upload) {

                ?>
                <ol id="how-i-did-list" class="row how-i-did-list">
                    <?php
                    $foody_how_i_did->the_comments();
                    ?>
                </ol><!-- .comment-list-->


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
                <script async defer>
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

    endif;

    foody_get_template_part(
        get_template_directory() . '/template-parts/content-image-upload-modal.php',
        array(
            'title' => $popup_title
        ));


    ?>

</div><!-- #comments -->
