<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/21/18
 * Time: 3:33 PM
 */

/** @var Foody_Recipe[] $recipes */
/** @noinspection PhpUndefinedVariableInspection */
$recipes = $template_args['recipes'];
$title = $template_args['title'];
?>


<ul class="playlist-recipes nolist">

    <li class="title-container">

        <h3>
            <i class="icon-heart"></i>
            <?php echo $title ?>
        </h3>
    </li>

    <?php foreach ($recipes as $recipe): ?>

        <li class="playlist-recipe-item row gutter-0">
            <div class="image-container col-6">
                <img src="<?php echo $recipe->getImage() ?>" alt="">
            </div>

            <div class="playlist-recipe-item-details col-6">
                <h3>
                    <?php echo $recipe->getTitle() ?>
                </h3>

                <a class="author-name" href="<?php echo get_author_posts_url($recipe->post->post_author) ?>">
                    <?php echo $recipe->getAuthorName() ?>
                </a>
            </div>


        </li>

    <?php endforeach; ?>

</ul>


