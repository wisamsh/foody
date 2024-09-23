<?php
/**
 * Template Name: Homepage New 2024
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */


get_header('new');

?>

    <div class="homepage">
        
        <div class="content">
            <div class="row recipes-grid gutter-10 featured">
                <?php //$homepage->featured() ?>
            </div>

            <?php //$homepage->categories_listing() ?>


            <?php //$homepage->promoted_items(); ?>


            <section class="feed-container row">


                <section class="sidebar-container d-none d-lg-block">
                    <?php

                   //// $homepage->sidebar('aside.sidebar-desktop  .sidebar-content');
                    ?>
                </section>


                <section class="content-container col-lg-9 col-12">

                    <!--                    --><?php //$homepage->feed(); ?>

                </section>

                <?php //Foody_Seo::seo() ?>

            </section>

        </div>

       

    </div>
<?php
//get_footer();