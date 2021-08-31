<?php
/**
 * Template Name: Foody Answer
 * Template Post Type:  foody_answer
 *
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$answer = new Foody_Answer();


?>



    <div id="main-content" class="main-content">
        <div id="primary" class="content-area">
            <div class="details-container">

                <!-- Top Banner -->
                <?php if ( get_field('answer_banner', $answer->get_id()) ) { ?>
                    <section class="top-banner no-print">
                        <img src="<?php echo $answer->our_banner() ?>" >
                    </section>
                <?php } ?>

                <div class="answer-container d-flex ">
                    <div class="answer-sidebar no-print">
                        <?php $answer->the_sidebar_content(); ?>
                    </div>

                    <div class="answer-details">
                        <div class="answer-breadcrumb">
                            <?php  bootstrap_breadcrumb(); ?>
                        </div>
                        <section class="recipe-details  d-flex">
                            <!-- Title -->
                            <section class="col-sm-11 col-10 contain-title">
                                <div class="row justify-content-between row-details m-0">
                                    <h1 class="col p-0">
                                        <?php echo $answer->our_post()->post_title ?>
                                    </h1>
                                </div>

                                <!-- Author image -->
                                <div class="image-container nopadding d-flex">
                                    <a href="<?php echo get_author_posts_url($answer->our_post()->post_author) ?>">
                                        <img src="<?php echo get_avatar_url($answer->our_post()->post_author, ['size' => 96]) ?>" alt="">
                                    </a>
                                    <div class="author-name">
                                        <?php
                                        echo $answer->our_author()->display_name;
                                        ?>
                                    </div>
                                </div>

                            </section>

                        </section>

                        <div class="description">

                            <!-- Bullets mobile-->
                            <section class="post-bullets-container d-block d-lg-none">

                            </section>

                            <!-- Description -->
                            <?php echo $answer->our_description() ?>

                        </div>

                        <!-- Image or video -->
                        <div class="<?php echo $answer->answer_has_video ? ' video-featured-content featured-content-container' : 'featured-content-container' ?> no-print">
                            <?php $answer->image_or_video() ?>
                        </div>


                        <!-- Related Questions -->
                        <section class="related-questions">
                            <?php $answer->get_similar_content('foody_answer', 'similar_content_group_questions') ?>
                        </section>


                        <!-- Related Recipes -->
                        <section class="recipe_similar_content no-print">
                            <?php $answer->get_similar_content('foody_recipe', 'similar_content_group' ) ?>
                        </section>

                        <?php if(has_category('',$answer->get_id())): ?>
                            <!-- FAQ Categories -->
                            <section class="answer-categories answer-taxonomy">
                                <?php $answer->the_categories_answer() ?>
                            </section>
                        <?php endif; ?>


                        <!-- FAQ Accessories -->
                        <section class="answer-accessories answer-taxonomy">
                            <?php $answer->the_accessories_answer() ?>
                        </section>

                        <!-- FAQ Techniques -->
                        <section class="answer-techniques answer-taxonomy">
                            <?php $answer->the_techniques_answer() ?>
                        </section>

                        <!-- FAQ Tags -->
                        <?php if ( $answer->has_tags_answer() ): ?>

                            <section class="answer-tags answer-taxonomy">
                                <h2 class="title">
                                    <?php echo __( 'תגיות', 'foody' ) ?>
                                </h2>
                                <?php $answer->the_tags_answer() ?>
                            </section>

                        <?php endif; ?>


                        <?php if ( !wp_is_mobile() ){ ?>
                            <section class="newsletter no-print">
                                <?php $answer->newsletter(); ?>
                            </section>
                        <?php } ?>

                    </div>
                </div>


            </div>




        </div><!-- #primary -->

    </div><!-- #main-content -->

<?php

get_footer();
