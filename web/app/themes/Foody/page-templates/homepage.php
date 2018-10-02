<?php
/**
 * Template Name: Homepage
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Malam WordPress 1.0
 */


get_header();


$homepage = new HomePage();
?>

    <div class="homepage">

        <?php $homepage->cover_photo() ?>


        <div class="content">
            <div class="row recipes-grid gutter-10 featured">
                <?php $homepage->featured() ?>
            </div>

            <?php $homepage->categories_listing() ?>

            <?php echo do_shortcode('[foody_team max="7" show_title="true"]') ?>

            <section class="feed-container row">

                <div class="feed-header d-none d-sm-block">
                    <h3 class="title d-sm-inline-block">
                        <?php __('Our Recommendations', 'foody') ?>
                        ההמלצות שלנו
                    </h3>
                </div>


                <?php
                if (!wp_is_mobile()) {
                    $homepage->sidebar();
                }

                ?>

                <section class="content-container col-sm-9 col-12">

                    <article class="feed row gutter-3 recipes-grid">
                        <?php $homepage->feed() ?>
                    </article>
                    <div class="show-more">
                        <img src="<?php echo $GLOBALS['images_dir'] . 'bite.png' ?>" alt="">
                        <h4>
                            לעוד מתכונים
                        </h4>
                    </div>

                </section>


            </section>


        </div>

        <?php if (wp_is_mobile()): ?>

            <!--        mobile filter -->
            <div class="filter-mobile d-block d-sm-none">
                <button class="navbar-toggler filter-btn" type="button" data-toggle="drawer"
                        data-target="#dw-p2">
                    <?php echo __('סינון', 'foody'); ?>
                </button>
            </div>

            <div class="mobile-filter d-sm-none">

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



        <?php endif; ?>
    </div>
<?php
get_footer();
