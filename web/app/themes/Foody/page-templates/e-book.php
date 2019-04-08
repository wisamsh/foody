<?php
/**
 * Template Name: E-Book
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();

$cta_text = get_field('cta_text');
$cta_link = get_field('cta_link');

$bg = $GLOBALS['images_dir'] . 'e-book-mobile.png';
if (!wp_is_mobile()) {
    $bg = $GLOBALS['images_dir'] . 'e-book.png';
}
$image = $GLOBALS['images_dir'] . 'e-book.png';
$mobile_image = $GLOBALS['images_dir'] . 'e-book-mobile.png';

$register_link = get_permalink(get_page_by_title('הרשמה'))
?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="contentt" class="site-content" role="main">
                <picture>
                    <source media="(min-width: 415px)" srcset="<?php echo $image ?>">
                    <source media="(max-width: 414px)"
                            srcset="<?php echo $mobile_image ?>">
                    <img src="<?php echo $image ?>">
                </picture>

                <a class="btn btn-primary cta cta-float" href="<?php echo $register_link ?>">
                    <?php echo __('להרשמה') ?>
                </a>

                <div class="text-center share-text">
                    <?php echo _('שתפו') ?>
                </div>

                <?php
                foody_get_template_part(get_template_directory() . '/template-parts/content-social-actions.php', ['exclude' => ['print']]);
                ?>

                <?php the_content(); ?>

                <section class="container pl-sm-0 pr-sm-0">
                    <section class="recipes row">
                        <figure class="col-12 col-sm-4">
                            <img src="<?php echo $GLOBALS['images_dir'] . 'pavlove.png' ?>" alt="">
                            <h3 class="title text-center">
                                <?php echo __('פבלובה של מיקי שמו') ?>
                            </h3>
                            <div class="description text-center">
                                <?php echo __('עוגת מרנג עם קציפת וניל ופירות') ?>
                            </div>
                        </figure>
                        <figure class="col-12 col-sm-4">
                            <img src="<?php echo $GLOBALS['images_dir'] . 'kinoa.png' ?>" alt="">
                            <h3 class="title text-center">
                                <?php echo __('סלט קינואה של שר פיטנס') ?>
                            </h3>
                            <div class="description text-center">
                                <?php echo __('סלט קינואה עם עשבי תיבול,חמוציות ואגוזים') ?>
                            </div>
                        </figure>
                        <figure class="col-12 col-sm-4">
                            <img src="<?php echo $GLOBALS['images_dir'] . 'salmon.png' ?>" alt="">
                            <h3 class="title text-center">
                                <?php echo __('סלמון של יונית צוקרמן') ?>
                            </h3>
                            <div class="description text-center">
                                <?php echo __('דג סלמון ברוטב מרוקאי חריף') ?>
                            </div>
                        </figure>

                    </section>

                </section>

                <a class="btn btn-primary cta" href="<?php echo $register_link ?>">
                    <?php echo __('להרשמה') ?>
                </a>


            </div><!-- #content -->

        </div><!-- #primary -->
    </div><!-- #main-content -->

<?php

get_footer();
