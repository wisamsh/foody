<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/21/18
 * Time: 3:33 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Playlist $playlist */
$playlist = $template_args['playlist'];
/** @var Foody_Recipe[] $recipes */
$recipes = $playlist->recipes;
$title = $template_args['title'];
$hide_title = isset($template_args['hide_title']) && $template_args['hide_title'];

?>


<ul class="playlist-recipes nolist">


    <?php if (!$hide_title) : ?>
        <li class="title-container">

            <h3>
                <i class="icon-Playlist"></i>
                <?php echo $title ?>
            </h3>
        </li>
    <?php endif; ?>
    <?php foreach ($recipes as $index => $recipe): ?>


        <?php
        if ($index == $playlist->get_current_recipe_index()) {
            $item_class = 'current disabled';
            $current_icon = '<i class="icon-timeplay"></i>';
        } else {
            $item_class = '';
            $current_icon = '';
        }

        ?>

        <li class="playlist-recipe-item row gutter-0 <?php echo $item_class ?>"
            data-video="<?php echo $recipe->video['id'] ?>">
            <div class="image-container col-6">
                <a href="<?php echo $playlist->get_playlist_recipe_link($recipe) ?>">
                    <figure class="tint">
                        <img src="<?php echo $recipe->getImage() ?>" alt="<?php echo $recipe->getTitle() ?>">
                        <?php
                        if (isset($current_icon) && !empty($current_icon)) {
                            echo $current_icon;
                        }
                        ?>
                    </figure>

                </a>
            </div>

            <div class="playlist-recipe-item-details col-6">
                <h3>
                    <a href="<?php echo $playlist->get_playlist_recipe_link($recipe) ?>">
                        <?php echo $recipe->getTitle() ?>
                    </a>
                </h3>

                <a class="author-name" href="<?php echo get_author_posts_url($recipe->post->post_author) ?>">
                    <?php echo $recipe->getAuthorName() ?>
                </a>
            </div>


        </li>

    <?php endforeach; ?>

</ul>


