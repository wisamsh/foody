<?php
/**
 * Template Name: Courses Cardcom Cancel
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */
global $post;
$course_id = '';
$has_course = false;
$has_content = false;
$has_cover = false;
$course_name = '';
$host_name = '';
$coupon_details = [];
$coupon_name = '';

if (isset($_GET)) {
    if (isset($_GET['course_id']) && isset($_GET['lowprofilecode'])) {
        $course_id = $_GET['course_id'];
        $has_course = true;
        $host_name = get_field('course_page_main_cover_section_host_name', $course_id);
        $course_name = get_field('course_register_data_item_name', $course_id);

        // set to cancelled
        $canceled = update_course_member_by_credit_low_profile_code_and_cloumns($_GET['lowprofilecode'], ['status' => 'canceled']);

        if ($canceled && isset($_GET['coupon']) && !empty($_GET['coupon'])) {
            $coupon_name = $_GET['coupon'];
            $coupon_details = get_coupon_data_by_name($_GET['coupon']);
            if ($coupon_details['type'] == 'unique') {
                $coupon_code_array = explode('_', $coupon_details['coupon_code']);
                update_unique_coupon_to_free($coupon_details['id'], $coupon_code_array[1]);
            } else {
                update_general_coupon_to_free($coupon_details['id']);
            }
        }
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
                <?php echo '<h1 class="title mt-0 mb-0"> העסקה בוטלה </h1>'; ?>
                <div class="foody-content">
                    <?php
                    if ($has_course) {
                        $content = get_field('cancelation_text');
                        if ($content) {
                            ?>
                            <p class="cancellation-text" data-course="<?php echo $course_name; ?>"
                               data-host="<?php echo $host_name; ?>"
                               data-coupon-used="<?php echo $coupon_name; ?>"> <?php echo $content; ?> </p>
                            <?php
                        } else {
                            the_content();
                        }
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
