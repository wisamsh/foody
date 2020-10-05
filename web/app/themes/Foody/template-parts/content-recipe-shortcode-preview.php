<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/21/18
 * Time: 2:48 PM
 */


/** @var Foody_Recipe $foody_page */
$foody_page = $template_args['recipe'];
$show_favorite = foody_get_array_default($template_args, 'show_favorite', true);
if (!foody_is_registration_open()) {
    $show_favorite = false;
}

if(is_array($_GET) && isset($_GET['referer'])){
    $short_code_link = $foody_page->link.'?referer='.$_GET['referer'];
}
else{
    $short_code_link = $foody_page->link;
}
?>

<div class="recipe-shortcode-container" onclick="window.open('<?php echo $short_code_link; ?>')">
<!--    <a href="--><?php //echo $foody_page->link ?><!--">-->
        <div class="details-container">

            <div class="<?php echo $foody_page->has_video ? ' video-featured-content featured-content-container' : '' ?>">
                <?php $foody_page->the_featured_content(true) ?>
            </div>

            <div class="details container">

                <div class="recipe-title col p-0">
                    <?php echo $foody_page->getTitle() ?>
                </div>

                <!-- Description -->
                <div class="description">
                    <?php echo $foody_page->getDescription() ?>
                </div>

                <section class="recipe-details  d-flex">

                    <!-- Author image -->
                    <div class="image-container col-lg-1 col-2 nopadding">
                        <a href="<?php echo $foody_page->get_author_link() ?>">
                            <img src="<?php echo $foody_page->getAuthorImage() ?>"
                                 alt="<?php echo $foody_page->getAuthorName() ?>">
                        </a>
                    </div>
                    <!-- Bullets desktop -->
                    <section class="col-lg-11 col-10  content-details-bullets-container">

                        <section class="wrapper">
                            <?php
                            $args = array(
                                'foody_page' => $foody_page,
                                'show_favorite' => $show_favorite,
                                'hide' => [
                                    'views' => true // wp_is_mobile()
                                ]
                            );

                            foody_get_template_part(get_template_directory() . '/template-parts/content-post-bullets.php', $args);

                            ?>

                            <section class="rating-container d-block d-lg-block">
                                <?php //Foody_Recipe::ratings() ?>
                                <?php
                                echo $foody_page->rating->foody_has_rating($foody_page->id) ?
                                $foody_page->rating->foody_get_populated_ratings($foody_page->id, false) : $foody_page->rating->foody_get_empty_stars();
                                ?>
                            </section>

                        </section>

                        <?php if ($show_favorite): ?>
                            <section class="favorite-container">
                                <?php

                                foody_get_template_part(
                                    get_template_directory() . '/template-parts/common/favorite.php',
                                    array(
                                        'post' => $foody_page,
                                        'show_text' => !wp_is_mobile()
                                    )
                                );
                                ?>
                            </section>
                        <?php endif; ?>

                    </section>

                </section>

            </div>

        </div>

        <div class="shortcode-recipe-link">

            הצג מתכון

        </div>
<!--    </a>-->
</div>
