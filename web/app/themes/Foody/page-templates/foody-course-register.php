<?php
/**
 * Template Name: Foody Course register
 *
 * Created by PhpStorm.
 * User: daniel
 */


get_header();
$course_id = isset( $_GET ) && isset( $_GET['course_id']) ? $_GET['course_id'] : '';
$register_page = new Foody_Course_register($course_id);
?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <section class="cover-section">
                <?php $register_page->get_cover_section(); ?>
            </section>
            <section class="form-section">
                <?php $register_page->get_form_section(); ?>
            </section>
            <?php $register_page->get_bottom_image(); ?>
        </div>

    </div>



<?php
get_footer();
