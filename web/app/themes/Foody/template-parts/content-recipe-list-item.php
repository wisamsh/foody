<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

/** @var Foody_Recipe $recipe */
/** @noinspection PhpUndefinedVariableInspection */
$recipe = $template_args['post'];
$args = foody_get_array_default($template_args, 'args', []);
$title_el = foody_get_array_default($args, 'title_el', 'h2');
$image_size = isset($args['image_size']) ? $args['image_size'] : 'list-item';

$target = '';
if (!empty($recipe->link_attrs['target'])) {
    $target = "target='{$recipe->link_attrs['target']}'";
}
$show_favorite = foody_get_array_default($template_args, 'show_favorite', true);
if (!foody_is_registration_open()) {
    $show_favorite = false;
}

$lazy = !empty($template_args['lazy']);

?>


<div class="recipe-item feed-item">
    <a href="<?php echo $recipe->link ?>" <?php echo $target ?>>
        <div class="image-container main-image-container">
            <?php if ($lazy): ?>
                <img src="<?php echo $GLOBALS['images_dir'] . 'recipe-placeholder.svg' ?>"
                     class="recipe-item-image feed-item-image lazyload"
                     data-foody-src="<?php echo $recipe->getImage() ?>"
                     alt="<?php echo foody_get_featured_image_alt($recipe) ?>">
            <?php else: ?>
                <img class="recipe-item-image feed-item-image" src="<?php echo $recipe->getImage() ?>"
                     alt="<?php echo foody_get_featured_image_alt($recipe) ?>">

            <?php endif; ?>

            <?php if (!empty($label = $recipe->get_label())): ?>

                <div class="recipe-label">
                    <span><?php echo $label ?> </span>
                </div>

            <?php endif; ?>

            <?php if (isset($recipe->post->pinned) && $recipe->post->pinned):

                $label = $recipe->get_pinned_recipe_lable($recipe->post->pinned);
                if (!empty($label)) {
                    ?>
                    <div class="recipe-label">
                        <span><?php echo $label ?> </span>
                    </div>
                <?php } ?>
            <?php endif; ?>


            <?php if (isset($args['feed_area_id']) && $args['feed_area_id'] && !empty($logo = $recipe->get_feed_logo($args['feed_area_id']))): ?>

                <img class="feed-logo-sticker" src="<?php echo $logo; ?>">

            <?php endif; ?>
            <?php if ($recipe->video != null): ?>
                <div class="duration">
                    <i class="icon icon-timeplay">

                    </i>
                    <span>
                        <?php echo $recipe->getDuration() ?>
                    </span>


                </div>
            <?php endif; ?>
        </div>
    </a>
    <section class="feed-item-details-container">
        <section class="title-container">
            <<?php echo $title_el ?> class="grid-item-title">
            <a href="<?php echo $recipe->link ?>" <?php echo $target ?>>
                <?php echo $recipe->getTitle() ?>
            </a>
        </<?php echo $title_el ?>>

        <div class="description">
            <?php echo $recipe->getDescription() ?>
        </div>
    </section>
    </section>

    <section class="recipe-item-details  d-flex">
        <div class="image-container col-12 nopadding">
            <a href="<?php echo $recipe->get_author_link() ?>">
                <img class="lazyload" src="<?php echo $GLOBALS['images_dir'] . 'author-placeholder.svg' ?>"
                     data-foody-src="<?php echo $recipe->getAuthorImage() ?>"
                     alt="<?php echo $recipe->getAuthorName() ?>">
            </a>
            <ul>
                <li>
                    <a href="<?php echo $recipe->get_author_link() ?>">
                        <?php echo $recipe->getAuthorName() ?>
                    </a>
                </li>
                <?php if (!empty(get_option('foody_show_post_views'))) : ?>
                    <li>
                        <?php echo $recipe->getViewCount() ?>
                    </li>
                <?php endif; ?>
                <?php if ($show_favorite): ?>
                    <li class="favorite-container">
                        <?php
                        foody_get_template_part(
                            get_template_directory() . '/template-parts/common/favorite.php',
                            array(
                                'id' => $recipe->id,
                                'post' => $recipe
                            )
                        );
                        ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </section>
</div>


