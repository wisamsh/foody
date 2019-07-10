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

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">

            <div class="course-information-top-section">

				<?php $course->get_information_top_section(); ?>

            </div>

        </div><!-- #primary -->

    </div><!-- #main-content -->

<?php

get_footer();
