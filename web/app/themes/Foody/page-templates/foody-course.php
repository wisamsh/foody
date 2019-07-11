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

    <!--    <div class="sticky-registration">-->
    <!--		--><?php
//		foody_get_template_part( get_template_directory() . '/template-parts/common/link.php', array( 'link' => $course->get_floating_registration_button() ) );
//		?>
    <!--    </div>-->
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
					foreach ( $how_it_works_steps as $step ) {
						echo '<span class="how-it-works-item">';
						echo '<span class="how-it-works-step-number">' . $step['step_number_text'] . '</span>';
						echo '<span class="how-it-works-step">' . $step['step_text'] . '</span>';
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

        </div><!-- #primary -->

    </div><!-- #main-content -->

<?php

get_footer();
