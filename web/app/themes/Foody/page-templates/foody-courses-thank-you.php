<?php
/**
 * Template Name: Courses Thank You
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */

$course_id = '';
$has_course = false;
$has_content = false;
$has_cover = false;
$course_name = '';
$host_name = '';

if (isset($_GET)) {
    if (isset($_GET['course_id'])) {
        $course_id = $_GET['course_id'];
        $has_course = true;
        $host_name = get_field('course_page_main_cover_section_host_name', $course_id);
        $course_name = get_field('course_register_data_item_name', $course_id);
    }
}
get_header();
?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="content" class="site-content" role="main">
                <?php
                if ($has_course) {
                $cover = get_field('course_register_data_cover_thank_you', $course_id);
                if (($cover != '' || $cover != false) && isset($cover['url'])) {
                ?>
                <div class="cover-image"></div>
                <img src="<?php echo $cover['url']; ?>" alt=""></div>
            <?php
            $has_cover = true;
            }
            }
            if (!$has_cover) { ?>
                <?php if (has_post_thumbnail($post->ID)): ?>
                    <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full'); ?>
                    <div class="cover-image">
                        <img src="<?php echo $image[0]; ?>" alt="">
                    </div>
                <?php endif; ?>
            <?php } ?>
            <div class="container container-max-880">
                <?php if (function_exists('bootstrap_breadcrumb')): ?>

                    <?php bootstrap_breadcrumb(); ?>

                <?php endif; ?>

                <?php echo the_title('<h1 class="title mt-0 mb-0">', '</h1>') ?>
                <div class="foody-content">

                    <?php
                    if ($has_course) {
                        $content = get_field('course_register_data_thank_you_text', $course_id);
                        if ($content != '' || $content != false) {
                            ?>
                            <p class="thank-you-text" data-course="<?php echo $course_name; ?>" data-host="<?php echo $host_name; ?>"> <?php echo $content; ?> </p>
                            <?php
                            $has_content = true;
                        }
                    }
                    if (!$has_content) {
                        the_content();
                    } ?>
                </div>

                <?php Foody_Seo::seo() ?>
            </div>


        </div><!-- #content -->

    </div><!-- #primary -->
    </div><!-- #main-content -->

<?php
//get_sidebar();
get_footer();
