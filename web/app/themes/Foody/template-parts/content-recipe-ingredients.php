<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/29/18
 * Time: 4:46 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$ingredients_groups = $template_args['groups'];

?>

<?php foreach ( $ingredients_groups as $ingredients_group ): ?>

    <div class="col-12 ingredients-group p-0">
        <h2 class="ingredients-group-title">
			<?php echo $ingredients_group['title']; ?>
        </h2>
        <ul class="ingredients-group-list">

			<?php /** @var Foody_Ingredient $ingredient */
			foreach ( $ingredients_group['ingredients'] as $ingredient ): ?>

                <li class="ingredients">

					<?php $ingredient->the_amounts() ?>
					<?php $ingredient->the_sponsored_ingredient(); ?>

                </li>

			<?php endforeach; ?>
        </ul>

    </div>


<?php endforeach; ?>


