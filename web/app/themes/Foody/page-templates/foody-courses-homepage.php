<?php

/**
 * Template Name: Foody Courses Homepage
 *
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$courses_homepage = new Foody_Courses_Homepage();

?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">

            <?php if ($courses_homepage->should_show_section('cover_section')): ?>
                <?php if ($courses_homepage->has_cover_video()) { ?>
                    <section class="course-cover">
                        <?php
                        $courses_homepage->get_cover_video();
                        ?>
                    </section>
                <?php } else { ?>
                    <?php
                    $cover_link = $courses_homepage->get_link('cover_section', 'cover_link');
                    if (!empty($cover_link)) { ?>
                        <a href="<?php echo $cover_link; ?>" class="cover-link">
                    <?php } ?>
                    <section class="course-cover">
                        <?php
                        $courses_homepage->get_cover_section('cover_section');
                        ?>
                    </section>
                    <?php if (!empty($cover_link)) { ?>
                        </a>
                    <?php }
                } ?>
            <?php endif; ?>

            <?php if ($courses_homepage->should_show_section('main_title_section')): ?>
                <?php
                $title_link = $courses_homepage->get_link('main_title_section', 'link');
                if (!empty($title_link)) { ?>
                    <a href="<?php echo $title_link; ?>" class="cover-link">
                <?php } ?>
                <section class="main-title-section">
                    <?php
                    $courses_homepage->get_cover_section('main_title_section');
                    ?>
                </section>
                <?php if (!empty($title_link)) { ?>
                    </a>
                <?php } ?>
            <?php endif; ?>

            <?php if ($courses_homepage->should_show_section('courses_section')): ?>
                <section class="courses-section">
                    <?php
                    $courses_homepage->get_courses_section();
                    ?>
                </section>
            <?php endif; ?>
            <?php if ($courses_homepage->should_show_section('advantages_section')): ?>
                <section class="advantages-section">
                    <?php
                    $courses_homepage->get_advantages_section();
                    ?>
                </section>
            <?php endif; ?>


        </div>
    </div>

<?php
get_footer();
