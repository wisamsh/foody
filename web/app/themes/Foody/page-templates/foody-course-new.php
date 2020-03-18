<?php
/**
 * Template Name: Foody Course new
 * Template Post Type: foody_course
 *
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$course = new Foody_Course_new();

$purchase_link = $course->get_floating_purchase_button();
$gift_link     = $course->get_floating_gift_button();
if ( ( ! empty( $purchase_link ) && isset( $purchase_link['url'] ) && ! empty( $purchase_link['url'] ) ) || ( ! empty( $gift_link ) && isset( $gift_link['url'] ) && ! empty( $gift_link['url'] ) ) ) {
	echo '<div class="sticky-registration"><div class="button-purchase">';
	if ( isset( $purchase_link['url'] ) ) {
		foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $purchase_link ) );
	}
    echo '</div>';
    echo '<div class="gift-purchase">';
	if ( isset( $gift_link['url'] ) ) {
		foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $gift_link ) );
	}
	echo '</div></div>';
}

?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">

			<?php if ( $course->should_show_section( 'main_cover_section' ) ): ?>

                <section class="course-cover">
					<?php
					$course->get_cover_section();
					?>
                </section>

			<?php endif; ?>

            <section class="gift-and-purchase-buttons">
				<?php
				$course->get_gift_and_purchase_buttons_div();
				?>
            </section>

			<?php if ( $course->should_show_section( 'what_waits_section' ) ): ?>

                <section class="what-waiting-section">
					<?php
					$course->get_whats_waiting_section();
					?>
                </section>

			<?php endif; ?>

			<?php if ( $course->should_show_section( 'course_video_group' ) ): ?>

                <section class="video-section">
					<?php
					$course->get_video_section();
					?>
                </section>

			<?php endif; ?>

			<?php if ( $course->should_show_section( 'banner_image_group' ) ) : ?>
                <section class="banner-section">
					<?php
					$course->get_banner_image();
					?>
                </section>
			<?php endif; ?>

			<?php if ( $course->should_show_section( 'alwayes_wanted_section' ) ): ?>
                <section class="always-wanted-section">
					<?php
					$course->get_always_wanted_section();
					?>
                </section>
			<?php endif; ?>

			<?php if ( $course->should_show_section( 'buy_kit_section' ) ): ?>
                <section class="buy-kit-section">
					<?php
					$course->get_buy_kit_section();
					?>
                </section>
			<?php endif; ?>

			<?php if ( $course->should_show_section( 'syllabus_section' ) ): ?>
                <section class="syllabus-section">
					<?php
					$course->get_syllabus_section();
					?>
                </section>
			<?php endif; ?>

			<?php if ( $course->should_show_section( 'faq_section' ) ): ?>
                <section class="faq-section">
					<?php
					$course->get_q_and_a_section();
					?>
                </section>
			<?php endif; ?>

			<?php if ( $course->should_show_section( 'testimonials_section' ) ): ?>
                <section class="testimonials-section">
					<?php
					$course->get_recommending_students();
					?>
                </section>
			<?php endif; ?>

			<?php if ( $course->should_show_section( 'recommended_section' ) ): ?>
                <section class="recommendations-section">
					<?php
					$course->get_recommendation_list();
					?>
                </section>
			<?php endif; ?>
        </div>
    </div>

<?php
get_footer();


