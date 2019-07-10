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

			<?php if ( $course->show_how_i_did ): ?>
                <div class="campaign-how-i-did">
					<?php $course->how_i_did() ?>
                </div>
			<?php endif; ?>
        </div><!-- #primary -->
    </div><!-- #main-content -->
<?php

get_footer();
