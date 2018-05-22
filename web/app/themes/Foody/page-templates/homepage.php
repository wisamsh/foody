<?php
/**
 * Template Name: Homepage
 *
 * @package WordPress
 * @subpackage Malam_WordPress
 * @since Malam WordPress 1.0
 */


get_header();

$homepage = new HomePage();

?>
    <div class="homepage">

        <?php $homepage->cover_photo() ?>


        <div class="content container-fluid">
            <div class="row recipes-grid">
                <?php $homepage->featured() ?>
            </div>

            <h3 class="title">קטגוריות</h3>


            <div class="categories-listing d-flex flex-row">
                <?php $homepage->categories_listing() ?>
            </div>

            <h3 class="title">הנבחרת שלנו</h3>

            <div class="team-listing" dir="rtl">
                <?php $homepage->team() ?>
            </div>

            <section class="feed-container row">

                <div class="feed-header d-none d-sm-block">
                    <div class="socials col-3">

                    </div>
                    <h3 class="col-9">
                        <?php __('Our Recommendations', 'Foody') ?>
                    </h3>
                </div>

                <aside class="sidebar sidebar-filter col-3 pl-0 d-none d-sm-block">
                    <section class="filter">
                        <input name="search" type="text" class="search" title="search">
                        <div class="filters">
                            <div id="accordion" role="tablist" class="foody-accordion">
                                <div class="">
                                    <div class="" role="tab" id="headingOne">
                                        <h5 class="mb-0">
                                            <a data-toggle="collapse" href="#collapseOne" aria-expanded="true"
                                               aria-controls="collapseOne">
                                                סינון
                                            </a>
                                        </h5>
                                    </div>

                                    <div id="collapseOne" class="collapse show" role="tabpanel"
                                         aria-labelledby="headingOne" data-parent="#accordion">
                                        <div class="card-body">
                                            <?php $homepage->filter() ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="social">
                            <section class="instagram">
                                <h3>

                                </h3>

                            </section>

                            <section class="facebook">
                                <h3>

                                </h3>

                            </section>
                        </div>
                        <div class="popular-recipes">
                            <section class="facebook">
                                <h3>
                                </h3>

                            </section>
                        </div>
                    </section>
                </aside>

                <section class="col-sm-9 col-12">
                    <article class="recommended-container recipes-grid d-none d-sm-block">
                        <?php $homepage->recommended() ?>
                    </article>

                    <article class="feed row recipes-grid">
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
        <div class="filter-mobile d-block d-sm-none">
            <a class="filter-btn" href="">
                סינון
            </a>
        </div>

    </div>


<?php
get_footer();
