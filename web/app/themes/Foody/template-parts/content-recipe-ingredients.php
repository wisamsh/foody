<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/29/18
 * Time: 4:46 PM
 */

$ingredients_groups = $template_args;

?>

<?php foreach ($ingredients_groups as $ingredients_group): ?>

    <div class="col-sm-6 col-12 ingredients-group p-0">
        <h4 class="ingredients-group-title">
            <?php echo $ingredients_group['title']; ?>
        </h4>
        <ul class="ingredients-group-list">

            <?php /** @var Foody_Ingredient $ingredient */
            foreach ($ingredients_group['ingredients'] as $ingredient): ?>

                <li class="ingredients">

                    <?php $ingredient->the_amounts() ?>

                </li>

            <?php endforeach; ?>

            <?php foreach ($ingredients_group['free_text_ingredients'] as $free_text_ingredient): ?>

                <li class="ingredients free-text-ingredients">
                    <?php echo $free_text_ingredient ?>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>


<?php endforeach; ?>


