<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/12/18
 * Time: 6:39 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$recipe_item = $template_args;
?>

<div class="image-container">
    <img class="recipe-item-image" src="<?php echo $recipe_item['image'] ?>" alt="<?php echo $recipe_item['title'] ?>">
	<?php if ( $recipe_item['has_video'] ): ?>
        <div class="duration">
            <i class="icon icon-timeplay">

            </i>
            <time>
				<?php echo $recipe_item['duration'] ?>
            </time>
        </div>
	<?php endif; ?>
</div>

