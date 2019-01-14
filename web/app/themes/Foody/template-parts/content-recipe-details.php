<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/21/18
 * Time: 2:48 PM
 */


/** @var Foody_Recipe $foody_page */
/** @noinspection PhpUndefinedVariableInspection */
$foody_page = $template_args['page'];
$show_favorite = foody_get_array_default($template_args, 'show_favorite', true);
?>
<div class="details container">
    <section class="recipe-details  d-flex">
        <div class="image-container col-sm-1 col-2 nopadding">
            <a href="<?php echo $foody_page->get_author_link() ?>">
                <img src="<?php echo $foody_page->getAuthorImage() ?>" alt="">
            </a>
        </div>
        <section class="col-sm-11 col-10">
            <div class="row justify-content-between m-0">
                <h1 class="col p-0">
                    <?php echo $foody_page->getTitle() ?>
                </h1>
            </div>
        </section>

    </section>

    <div class="description">
        <section class="post-bullets-container d-block d-lg-none">
            <?php

            $args = array(
                'foody_page' => $foody_page
            );

            foody_get_template_part(get_template_directory() . '/template-parts/content-post-bullets.php', $args);

            ?>
        </section>
        <section class="d-block d-lg-none">
            <?php Foody_Recipe::ratings() ?>
        </section>
        <?php echo $foody_page->getDescription() ?>

    </div>

    <section class="d-none d-lg-block content-details-bullets-container">
        <?php


        $args = array(
            'foody_page' => $foody_page,
            'show_favorite' => $show_favorite
        );

        //        if ($foody_page->has_rating()) {
        //            $rating_args = [
        //                'value' => get_post_rating($foody_page->id),
        //                'disabled' => true,
        //                'hide_title' => true,
        //                'size' => 'data-size="xs"',
        //                'show_value' => true,
        //                'return' => true
        //            ];
        //
        //            $rating = foody_get_template_part(
        //                get_template_directory() . '/template-parts/content-rating.php',
        //                $rating_args
        //            );
        //
        //            $rating = do_shortcode('[ratings]');
        //
        //            $args['dynamic'] = [
        //                $rating
        //            ];
        //        }

        foody_get_template_part(get_template_directory() . '/template-parts/content-post-bullets.php', $args);


        ?>

    </section>
    <?php if ($show_favorite): ?>
        <section class="d-block d-xl-none favorite-container">
            <?php

            foody_get_template_part(
                get_template_directory() . '/template-parts/common/favorite.php',
                array(
                    'post' => $foody_page
                )
            );
            ?>
        </section>
    <?php endif; ?>

    <section class="">
        <?php foody_get_template_part(get_template_directory() . '/template-parts/content-social-actions.php', ['recipe' => $foody_page]); ?>
    </section>


</div>
