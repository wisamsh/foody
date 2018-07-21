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

                                            <section class="d-none d-sm-block">
                                                <?php
                                                foody_get_template_part(
                                                    get_template_directory() . '/template-parts/content-social-actions.php'
                                                )
                                                ?>
                                            </section>

                                        </div>

                                        <div class="description">
                                            <section class="post-bullets-container d-block d-sm-none">
                                                <?php

                                                $args = array(
                                                    'foody_page' => $foody_page,
                                                    'show_favorite' => false
                                                );
                                                foody_get_template_part(get_template_directory() . '/template-parts/content-post-bullets.php', $args);

                                                ?>
                                            </section>
                                            <?php echo $foody_page->getDescription() ?>
                                        </div>

                                        <?php

                                        if (!wp_is_mobile()) {
                                            $args = array(
                                                'foody_page' => $foody_page,
                                                'show_favorite' => true
                                            );
                                            foody_get_template_part(get_template_directory() . '/template-parts/content-post-bullets.php', $args);
                                        } else {
                                            foody_get_template_part(get_template_directory() . '/template-parts/common/favorite.php');
                                        }

                                        ?>
                                    </section>



                                </section>


                                <?php
                                if (wp_is_mobile()) {
                                    foody_get_template_part(get_template_directory() . '/template-parts/content-social-actions.php');
                                }
                                ?>

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
