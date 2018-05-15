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
            <?php $homepage->featured() ?>


            <h3 class="title">קטגוריות</h3>


            <div class="categories-listing d-flex flex-row">
                <?php $homepage->categories_listing() ?>
            </div>

            <h3 class="title">הנבחרת שלנו</h3>

            <div class="team-listing" dir="rtl">
                <?php $homepage->team() ?>
            </div>

            <section class="feed-container row">

                <div class="feed-header">
                    <div class="socials">

                    </div>
                    <h3>
                       <?php __('Our Recommendations','Foody') ?>
                    </h3>
                </div>

                <aside class="sidebar sidebar-filter col-3">

                </aside>

                <article class="feed col-9">

                </article>

            </section>


        </div>


    </div>


<?php
get_footer();
