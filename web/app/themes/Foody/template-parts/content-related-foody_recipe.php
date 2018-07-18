<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/12/18
 * Time: 6:39 PM
 */

$recipe_item = $template_args;
?>

<div class="image-container">
    <img class="recipe-item-image" src="<?php echo $recipe_item['image'] ?>" alt="">
    <div class="duration">
        <i class="icon icon-timeplay">

        </i>
        <time>
           <?php echo $recipe_item['duration'] ?>
        </time>
    </div>
</div>

