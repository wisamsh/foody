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


                <aside class="sidebar col d-none d-sm-block pl-0">
                    <input name="search" type="text" class="search" title="search" placeholder="חיפוש מתכון…">
                    <div class="sidebar-content">
                        <?php $homepage->filter() ?>
                    </div>

                </aside>

                <section class="content-container col-sm-9 col-12">

                    <article class="feed row gutter-3 recipes-grid">
                        <?php $homepage->feed() ?>

                        <div class="show-more">
                            <img src="<?php echo $GLOBALS['images_dir'] . 'bite.png' ?>" alt="">
                            <h4>
                                לעוד מתכונים
                            </h4>
                        </div>

                    </article>
                </section>


            </section>


        </div>

        <!--        mobile filter -->
        <div class="filter-mobile d-block d-sm-none">
            <button class="navbar-toggler filter-btn" type="button" data-toggle="drawer"
                    data-target="#dw-p2">
                סינון
            </button>


        </div>
    </div>
<?php
get_footer();
