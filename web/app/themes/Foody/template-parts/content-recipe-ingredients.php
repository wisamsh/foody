<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/29/18
 * Time: 4:46 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$ingredients_groups = $template_args['groups'];
$substitute_ingredients_filters = isset($template_args['substitute_ingredients_filters']) ? $template_args['substitute_ingredients_filters'] : null;
$counter = 0;
$recipe_id = isset($template_args['recipe_id']) ? $template_args['recipe_id'] : false;
?>

<?php foreach ($ingredients_groups as $ingredients_group): ?>

    <div class="col-12 ingredients-group p-0">
        <h2 class="ingredients-group-title">
            <?php echo $ingredients_group['title']; ?>
        </h2>
        <ul class="ingredients-group-list">

            <?php /** @var Foody_Ingredient $ingredient */
            foreach ($ingredients_group['ingredients'] as $ingredient): ?>
                <?php
                $id = "ingredient-" . $counter;
                $counter++;
                ?>
                <li class="ingredients" id="<?php echo $id; ?>">
                    <div class="ingredient">
                        <?php $ingredient->the_amounts(true, $recipe_id) ?>
                    </div>
                    <?php $ingredient->the_sponsored_ingredient(true, $recipe_id); ?>
                    <?php echo $ingredient->get_substitute_ingredient($substitute_ingredients_filters, $recipe_id); ?>
                    <?php
                    // Add ingredient comment
                    if (!empty($ingredient->comment)) {
                        echo '<div class="comment">' . $ingredient->comment . '</div>';
                    }
                    ?>

                </li>

            <?php endforeach; ?>
        </ul>

    </div>


<?php endforeach; ?>


