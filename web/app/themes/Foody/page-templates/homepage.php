<?php
/**
 * Template Name: Homepage
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Malam WordPress 1.0
 */


get_header();


$homepage = new Foody_HomePage();
?>

    <div class="homepage">

        <?php $homepage->cover_photo() ?>

        <div class="content">
            <div class="row recipes-grid gutter-10 featured">
                <?php $homepage->featured() ?>
            </div>

            <?php $homepage->categories_listing() ?>

            <?php echo do_shortcode('[foody_team max="6" show_title="true"]') ?>

            <section class="feed-container row">


                <section class="sidebar-container d-none d-lg-block">
                    <?php
                    $homepage->sidebar();
                    ?>
                </section>


                <section class="content-container col-lg-9 col-12">

                    <?php $homepage->feed(); ?>

                </section>


            </section>


        </div>

        <div class="filter-mobile d-block d-lg-none">
            <button class="navbar-toggler filter-btn" type="button" data-toggle="drawer"
                    data-target="#dw-p2">
                <?php echo __('סינון', 'foody'); ?>
            </button>
        </div>

        <div class="mobile-filter d-lg-none">

            <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <?php $homepage->sidebar() ?>

            <div class="show-recipes-container">

                <button class="btn show-recipes">
                    <?php echo __('הצג מתכונים', 'foody') ?>
                </button>
            </div>
        </div>

    </div>
<?php
get_footer();
