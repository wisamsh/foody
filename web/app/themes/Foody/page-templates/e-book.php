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

?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="contentt" class="site-content" role="main">
                <div class="container container-max-880">

                    <?php echo the_title( '<h1 class="title mt-0 mb-0">', '</h1>' ) ?>

                    <?php the_content(); ?>

                    <?php Foody_Seo::seo() ?>
                </div>


            </div><!-- #content -->

        </div><!-- #primary -->
    </div><!-- #main-content -->

<?php

get_footer();
