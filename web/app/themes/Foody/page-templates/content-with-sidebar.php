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

                        foody_set_post_views(get_the_ID());

                        ?>
                        <section class="details-container">
                            <div class="video-container">
                                <?php $foody_page->the_featured_content() ?>
                            </div>

                            <div class="details container">
                                <?php echo get_the_category_list() ?>

                                <section class="recipe-details  d-flex">
                                    <div class="image-container col-sm-1 col-2 nopadding">
                                        <img src="<?php echo $foody_page->getAuthorImage() ?>" alt="">
                                    </div>
                                    <section class="col-sm-11 col-10">
                                        <div class="row justify-content-between m-0">
                                            <h1 class="col p-0">
                                                <?php echo $foody_page->getTitle() ?>
                                            </h1>

                                            <div class=" social col p-0">
                                                <?php echo do_shortcode('[easy-social-share buttons="print,mail,pinterest,whatsapp" template="11" counters=0 style="icon" point_type="simple"]'); ?>

                                            </div>


                                        </div>

                                        <div class="description">
                                            <?php echo $foody_page->getDescription() ?>
                                        </div>
                                        <ul class="content-details-bullets">
                                            <li>
                                                <?php echo $foody_page->getAuthorName() ?>
                                            </li>
                                            <li>
                                                <?php echo $foody_page->getViewCount() ?>
                                            </li>
                                            <li>
                                                <?php echo $foody_page->getPostedOn() ?>
                                            </li>
                                        </ul>


                                        <div class="favorite">
                                            <i class="icon-heart">

                                            </i>
                                            <span>
                    הוספה למועדפים
                </span>
                                        </div>
                                    </section>


                                </section>

                            </div>


                        </section>
                        <?php

                        get_template_part('template-parts/single', get_post_type());

                        edit_post_link();

                    endwhile; // End of the loop.
                    ?>
                </article>

            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
//get_sidebar();
get_footer();
