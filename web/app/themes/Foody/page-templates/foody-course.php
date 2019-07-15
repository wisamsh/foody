<?php
/**
 * Template Name: Foody Course
 * Template Post Type: foody_course
 *
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$course = new Foody_Course();

?>

    <div class="sticky-registration">
		<?php
		foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $course->get_floating_registration_button() ) );
		?>
    </div>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
			<?php
			$image = $course->get_cover_image();
			foody_get_template_part( get_template_directory() . '/template-parts/content-cover-image.php', array(
				'image' => $image,
				'link'  => ''
			) );
			?>

            <div class="cover-video">
				<?php the_field( 'video' ); ?>
            </div>

            <div class="about">
				<?php the_field( 'about' ); ?>
            </div>

            <div class="course-information">

                <div class="course-information-top-section">

					<?php
					$information = $course->get_information_top_section();
					foreach ( $information as $information_item ) {
						echo '<span class="information-item">';
						echo '<div class="title information-title">' . $information_item['title'] . '</div>';
						echo '<div class="information-subtitle">' . $information_item['subtitle'] . '</div>';
						echo '</span>';
					}
					?>

                </div>

                <div class="course-information-bottom-section">

                    <div class="information-item price">
						<?php
						$information = $course->get_information_bottom_section();
						if ( ! empty( $information ) ) {
							echo '<span class="price-text">' . $information['price_text'] . '</span>';
							echo '<span class="price-number">' . '₪' . $information['price'] . '</span>';
						}
						?>
                    </div>

                    <span class="information-item information-registration-link">
                        <?php
                        foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $course->get_information_registration_link() ) );
                        ?>
                </div>

            </div>


            <div class="course-is-for">

                <div class="title course-is-for-title">
					<?php echo $course->get_course_is_for_title(); ?>
                </div>
                <ul class="course-is-for-bullets">

					<?php
					$course_is_for_bullets = $course->get_course_is_for();
					foreach ( $course_is_for_bullets as $bullet ) {
						echo '<li class="course-is-for-item">';
						echo '<span class="course-is-for-bullet">' . $bullet['bullet_text'] . '</span>';
						echo '</li>';
					}
					?>

                </ul>

            </div>

            <div class="how-it-works">

                <div class="title how-it-works-title">
					<?php echo $course->get_how_it_works_title(); ?>
                </div>
                <div class="how-it-works-bullets">

					<?php
					$how_it_works_steps = $course->get_how_it_works();
					foreach ( $how_it_works_steps as $index => $step ) {
						echo '<span class="how-it-works-item">';
						echo '<span class="how-it-works-step-number">' . $step['step_number_text'] . '</span>';
						echo '<span class="how-it-works-step">' . $step['step_text'] . '</span>';
						if ( $index !== count( $how_it_works_steps ) - 1 ) {
							echo '<hr class="divider"/>';
						}
						echo '</span>';
					}
					?>

                </div>

                <span class="how-it-works-registration-link">
                    <?php
                    foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $course->get_how_it_works_registration_link() ) );
                    ?>
                </span>
            </div>

            <div class="course-plan">

                <div class="title course-plan-title">
					<?php echo $course->get_course_plan_title(); ?>
                </div>
                <div class="course-plan-container">

                    <div class="course-plan-classes">
						<?php
						$course_plan_classes = $course->get_course_plan_classes();
						foreach ( $course_plan_classes as $index => $class ) {
							echo '<div class="course-class-item">';
							echo '<span class="course-class-number">' . ( $index + 1 ) . '</span>';
							echo '<span class="course-class-name">' . $class['class_name'] . '</span>';
							echo '<span class="course-class-info">' . $class['class_info'] . '</span>';
							echo '</div>';
						}
						?>

                    </div>

                    <span class="classes-registration-link">
                    <?php
                    foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $course->get_course_plan_registration_link() ) );
                    ?>
                </span>
                </div>
            </div>

            <div class="course-promotions">
				<?php
				$promotions = $course->get_promotions();
				foreach ( $promotions as $promotion ) {
					$link  = isset( $promotion['link'] ) ? $promotion['link'] : '';
					$image = $promotion['image'];

					echo '<span class="course-promotion">';
					if ( ! empty( $link ) ) {
						echo '<a href="' . $link['url'] . '" target="' . $link['target'] . '">';
					}
					echo '<img src="' . $image['url'] . '">';

					if ( ! empty( $link ) ) {
						echo '</a>';
					}

					echo '</span>';
				}
				?>
            </div>

            <div class="course-coupon-promotions">
				<?php
				$coupon_promotions = $course->get_coupon_promotions();
				foreach ( $coupon_promotions as $coupon_promotion ) {
					$link  = isset( $coupon_promotion['link'] ) ? $coupon_promotion['link'] : '';
					$image = $coupon_promotion['image'];

					echo '<span class="course-coupon-promotion">';
					if ( ! empty( $link ) ) {
						echo '<a href="' . $link['url'] . '" target="' . $link['target'] . '">';
					}
					echo '<img src="' . $image['url'] . '">';

					if ( ! empty( $link ) ) {
						echo '</a>';
					}

					echo '</span>';
				}
				?>
            </div>


            <div class="recommendations">

                <div class="title recommendations-title">
					<?php echo $course->get_recommendations_title(); ?>
                </div>

				<?php
				$slider_data = [
					'slidesToShow'   => 1,
					'rtl'            => true,
					'prevArrow'      => '<i class="icon-arrowleft prev"></i>',
					'nextArrow'      => '<i class="icon-arrowleft next"></i>',
					'dots'           => true,
					'slidesToScroll' => 1,
					'infinite'       => true
				]
				?>
                <div class="recommendations-container"
                     data-slick='<?php echo json_encode( $slider_data, ENT_QUOTES ) ?>'>

					<?php
					$recommendations = $course->get_recommendations();
					foreach ( $recommendations as $recommendation ) {
						echo '<div class="recommendation-item">';
						echo '<div class="recommendation-text">' . $recommendation['text'] . '</div>';
						echo '<div class="recommendation-name">' . $recommendation['name'] . '</div>';
						echo '</div>';
					}
					?>

                </div>

            </div>

            <div class="divider"></div>

            <div class="legal-text">
				<?php $course->the_legal_text() ?>
            </div>

            <div class="legal-registration-link">
				<?php
				foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $course->get_legal_registration_link() ) );
				?>
            </div>

            <section class="course-share socials">
				<?php foody_get_template_part( get_template_directory() . '/template-parts/content-social-actions.php', [ 'exclude' => [ 'print' ] ] ) ?>
            </section>

            <section class="newsletter-container no-print">
				<?php
				foody_get_template_part( get_template_directory() . '/template-parts/content-newsletter.php', [
					'button_classes' => 'col-2',
					'input_classes'  => 'col-10',
					'title'          => $course->get_newsletter_title(),
					'checkbox_text'  => $course->get_newsletter_checkbox_text()
				] );
				?>
            </section>

        </div><!-- #primary -->

    </div><!-- #main-content -->

<?php

get_footer();
