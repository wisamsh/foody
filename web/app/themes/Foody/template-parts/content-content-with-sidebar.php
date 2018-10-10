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
?>

    <script>
        post = {
            ID: '<?php echo get_the_ID() ?>',
            type: '<?php echo get_post_type()?>'
        };
    </script>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="row m-0">
                <?php if (!$hide_progress): ?>
                    <div class="progress-wrapper">
                        <progress dir="ltr"></progress>
                    </div>

                <?php endif; ?>

                <aside class="col d-none d-sm-block">

                    <?php $foody_page->the_sidebar_content() ?>

                </aside>

                <article class="content">
                    <?php
                    if (have_posts() && !is_search()) {
                        while (have_posts()) :
                            the_post();
                            foody_set_post_views($foody_page->getId());

                            ?>
                            <section class="details-container">
                                <div class="featured-content-container">
                                    <?php $foody_page->the_featured_content() ?>
                                </div>

                                <?php $foody_page->the_details() ?>

                            </section>
                            <?php

                            $foody_page->the_content($foody_page);

                        endwhile; // End of the loop.
                    } elseif (is_author() || is_search() || is_category()) {
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
                    //                    elseif (is_category()) {
                    //                        ?>
                    <!--                        <section class="details-container">-->
                    <!--                            <div class="featured-content-container">-->
                    <!--                                --><?php //$foody_page->the_featured_content() ?>
                    <!--                            </div>-->
                    <!---->
                    <!--                            --><?php //$foody_page->the_details() ?>
                    <!---->
                    <!--                        </section>-->
                    <!--                        --><?php
                    //
                    //                        $foody_page->the_content($foody_page);
                    //                    } elseif (is_search()) {
                    //                        ?>
                    <!--                        <section class="details-container">-->
                    <!--                            <div class="featured-content-container">-->
                    <!--                                --><?php //$foody_page->the_featured_content() ?>
                    <!--                            </div>-->
                    <!---->
                    <!--                            --><?php //$foody_page->the_details() ?>
                    <!---->
                    <!--                        </section>-->
                    <!--                        --><?php
                    //
                    //                        $foody_page->the_content($foody_page);
                    //                    }

                    ?>
                </article>

            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
//get_sidebar();
get_footer();