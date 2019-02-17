<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/21/18
 * Time: 6:42 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Playlist $playlist */
$playlist = $template_args['playlist'];

$current_recipe = $playlist->get_current_recipe();
$current_recipe_index = $playlist->get_current_recipe_index();

$prev_class = $current_recipe_index == 0 ? 'disabled' : '';
$next_class = $current_recipe_index == $playlist->num_of_recipes - 1 ? 'disabled' : '';

?>

<section class="d-none d-lg-block">
    <?php
    $current_recipe->the_details();
    ?>
</section>
<div class="details-container d-block d-lg-none">

    <div class="details">
        <section class="playlist-details">

            <div class="title-container row">

                <i class="icon-Playlist col-1"></i>
                <h1 class="col playlist-title">

                    <?php echo $playlist->getTitle() ?>
                </h1>
            </div>
            <div class="actions row">
                <button class="btn btn-prev col-1 offset-1 <?php echo $prev_class ?>">
                    <a href="<?php echo $playlist->prev() ?>">
                        <i class="icon-Backw"></i>
                    </a>
                </button>

                <button class="btn btn-next col-1 <?php echo $next_class ?>">
                    <a href="<?php echo $playlist->next() ?>">
                        <i class="icon-Forw"></i>
                    </a>
                </button>

                <span class="count col">
            <?php
            $count = $playlist->num_of_recipes;
            $current = $playlist->get_current_recipe_index() + 1;
            printf('%s/%s', $current, $count);
            ?>
        </span>
            </div>

            <?php
            foody_get_template_part(
                get_template_directory() . '/template-parts/content-social-actions.php'
            )
            ?>
        </section>
        <button class="btn btn-show-recipe">
            <a role="button" href="<?php echo get_permalink($playlist->get_current_recipe()->id) ?>">
                <span>הצג מתכון</span>
                <span>
                    <i class="icon-arrowleft"></i>
                </span>
            </a>
        </button>
    </div>
</div>