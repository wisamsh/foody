<?php
/**
 * Template Name: Content with Images
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */

get_header();

$image_right = get_field( 'right_image' );
$image_left  = get_field( 'left_image' );

$cover_image = get_field('cover_image');
$cover_image_mobile = get_field('mobile_cover_image');
global $post;
if(isset($cover_image['url']) || isset($cover_image_mobile['url'])) {
    foody_get_template_part(get_template_directory() . '/template-parts/content-cover-image.php', array(
        'image' => $cover_image,
        'mobile_image' => $cover_image_mobile,
        'link' => '',
        'is_register' => isset($post) && isset($post->post_title) && $post->post_title == __('הרשמה')
    ));
}
?>

    <div class="content-with-images container-fluid foody-content">

        <div class="row">

            <div class="image-container image-container-right col d-sm-none d-lg-block">
                <img src=" <?php echo $image_right['url'] ?>" alt="<?php echo $image_right['alt'] ?>">
            </div>

            <div class="content col-12 col-lg-6">
				<?php

				the_content();

				?>
            </div>

            <div class="image-container image-container-left col d-sm-none d-lg-block">
                <img src=" <?php echo $image_left['url'] ?>" alt="<?php echo $image_left['alt'] ?>">
            </div>

        </div>


    </div>


<?php
get_footer();


