<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/1/18
 * Time: 2:30 PM
 */

get_header();

$hide_progress = isset($template_args['hide_progress']) && $template_args['hide_progress'];


$foody_page = Foody_PageContentFactory::get_instance()->get_page();


if (method_exists($foody_page, 'featured_content_classes')) {
    $featured_content_classes = $foody_page->featured_content_classes();
} else {
    $featured_content_classes = [];
}


$featured_content_classes[] = 'featured-content-container';

?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="row m-0">
                <?php if (!$hide_progress): ?>
                    <div class="progress-wrapper">
                        <progress dir="ltr"></progress>
                    </div>

                <?php endif; ?>

                <?php if (method_exists($foody_page, 'before_content')){
                    $foody_page->before_content();
                } ?>


                <aside class="col d-none d-lg-block">

                    <?php $foody_page->the_sidebar_content() ?>

                </aside>

                <article class="content">
                    <?php
                    if (have_posts() && !is_search() && is_single()) {
                        while (have_posts()) :
                            the_post();
                            foody_set_post_views($foody_page->getId());

                            ?>
                            <section class="details-container">
                                <div class="<?php foody_el_classes($featured_content_classes) ?>">
                                    <?php $foody_page->the_featured_content() ?>
                                </div>

                                <?php $foody_page->the_details() ?>

                            </section>
                            <?php

                            $foody_page->the_content($foody_page);

                        endwhile; // End of the loop.
                    } elseif (is_author() || is_search() || is_category() || is_tag()) {
                        ?>
                        <section class="details-container">
                            <div class="featured-content-container">
                                <?php $foody_page->the_featured_content() ?>
                            </div>

                            <?php $foody_page->the_details() ?>

                        </section>
                        <?php

                        $foody_page->the_content($foody_page);

                    }

                    ?>
                </article>

            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();