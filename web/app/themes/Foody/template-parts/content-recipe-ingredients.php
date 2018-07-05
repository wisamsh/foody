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

    <div class="col ingredients-group p-0">
        <h4 class="ingredients-group-title">
            <?php echo $ingredients_group['title']; ?>
        </h4>
        <ul class="ingredients-group-list">

            <?php foreach ($ingredients_group['ingredients'] as $ingredient): ?>

                <li class="ingredients" data-amount="<?php echo $ingredient->amount; ?>" data-unit="<?php echo $ingredient->amount; ?>">
                    <span class="amount">
                        <?php echo $ingredient->amount; ?>
                    </span>
                    <span class="unit">
                        <?php echo $ingredient->getUnit(); ?>
                    </span>
                    <span class="unit">
                        <?php echo $ingredient->getTitle(); ?>
                    </span>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>


<?php endforeach; ?>


