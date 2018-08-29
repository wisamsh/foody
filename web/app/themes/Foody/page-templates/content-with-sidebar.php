<?php
/**
 * Template Name: Content With Sidebar
 * Template Post Type: post, foody_recipe, foody_article, foody_playlist
 *
 *
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:21 AM
 */

get_header();

$foody_page = Foody_PageContentFactory::get_instance()->get_page();

?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="row m-0">
                <div class="progress-wrapper">
                    <progress dir="ltr"></progress>
                </div>

                <aside class="col d-none d-sm-block">

                    <?php $foody_page->the_sidebar_content() ?>

                </aside>

                <article class="content">
                    <?php
                    while (have_posts()) :
                        the_post();
                        foody_set_post_views($foody_page->getId());

                        ?>
                        <section class="details-container">
                            <div class="video-container">
                                <?php $foody_page->the_featured_content() ?>
                            </div>

                            <?php $foody_page->the_details() ?>

                        </section>
                        <?php

                        get_template_part('template-parts/single', get_post_type());


                    endwhile; // End of the loop.
                    ?>
                </article>

            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
//get_sidebar();
get_footer();
