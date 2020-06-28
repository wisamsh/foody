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
$coupon_name = '';

if (isset($_GET)) {
    $payment_initiation_id = false;
    if (isset($_GET['course_id'])) {
        $course_id = $_GET['course_id'];
        $payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : false;
        $payment_status = $payment_method && isset($_GET['status']) ? $_GET['status'] : false;
        $payment_initiation_id = $payment_method && isset($_GET['paymentInitiation']) ? $_GET['paymentInitiation'] : false;
        $credit_low_profile_code = isset($_GET['lowprofilecode']) ? $_GET['lowprofilecode'] : false;
        $canceled = $credit_low_profile_code && isset($_GET['canceled']) ? $_GET['canceled'] : false;
        $has_course = true;
        if (strpos($course_id, ',') != false) {
            $params = explode(',', $course_id);
            if (is_array($params) && isset($params[0]) && isset($params[1])) {
                $course_id = $params[0];
                $payment_initiation_id = $params[1];
            }
        }
        $host_name = get_field('course_page_main_cover_section_host_name', $course_id);
        $course_name = get_field('course_register_data_item_name', $course_id);
        if ($payment_method && $payment_method == __('ביט')) {
            apply_filters('body_class', []);
        }
    }

    if (wp_is_mobile() && $payment_initiation_id) {
        $status = get_payment_status($payment_initiation_id);
        if (!is_array($status)) {
            if ($status == 2 || $status == 3 || $status == 7) {
                $payment_method = true;
                $payment_status = 'canceled';
            } else {
                $payment_method = true;
                $payment_status = 'approved';
            }
        }
    }

    if ($payment_initiation_id) {
        $coupon_name = get_coupon_by_payment_initiation_id($payment_initiation_id);
    } elseif ($credit_low_profile_code){
        if(!$canceled) {
            $coupon_name = get_coupon_by_credit_low_profile_code($credit_low_profile_code);
            check_cardcom_purchase($credit_low_profile_code);
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
                <?php if ($payment_method && $payment_status && $payment_status == 'canceled') {
                    echo '<h1 class="title mt-0 mb-0"> העסקה בוטלה </h1>';
                } else {
                    echo the_title('<h1 class="title mt-0 mb-0">', '</h1>');
                } ?>
                <div class="foody-content">
                    <?php
                    if ($has_course) {
                        if ($payment_method && $payment_status && $payment_status == 'canceled') {
                            $content = get_field('course_register_data_bit_cancel_text', $course_id);
                            $canceled = true;
                        } elseif ($payment_method && $payment_status && $payment_status == 'approved') {
                            $content = get_field('course_register_data_bit_thank_you_text', $course_id);
                            $canceled = false;
                        } else {
                            $content = get_field('course_register_data_thank_you_text', $course_id);
                            $canceled = false;

                        }
                        if ($content != '' || $content != false) {
                            $page_content_class = $canceled ? 'cancellation-text' : 'thank-you-text';
                            ?>
                            <p class="<?php echo $page_content_class; ?>" data-course="<?php echo $course_name; ?>"
                               data-host="<?php echo $host_name; ?>"
                               data-coupon-used="<?php echo $coupon_name; ?>"> <?php echo $content; ?> </p>
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
