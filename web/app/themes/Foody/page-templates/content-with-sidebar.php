<?php
/**
 * Template Name: Content With Sidebar
 * Template Post Type: post, foody_recipe, foody_article
 *
 *
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:21 AM
 */

get_header();

?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <div class="row m-0">
                <div class="progress-wrapper">
                    <progress dir="ltr"></progress>
                </div>

                <aside class="col d-none d-sm-block">


                </aside>

                <article class="content">
                    <?php
                    while (have_posts()) :
                        the_post();

                        foody_set_post_views(get_the_ID());

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
