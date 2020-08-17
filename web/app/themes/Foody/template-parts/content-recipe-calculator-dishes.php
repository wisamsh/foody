<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/21/18
 * Time: 10:06 AM
 */
/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Recipe $recipe */
$recipe = $template_args['recipe'];
?>

<label for="number-of-dishes">
    <?php echo $recipe->amount_for ?>
</label>
<div class="amount-container">
    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/plus.svg' ?>" alt="plus" class="plus-icon">
    <input name="amount" type="number" id="number-of-dishes" min="1" inputmode="numeric" pattern="[0-9]*"
           value="<?php echo $recipe->getNumberOfDishes() ?>"
           data-amount="<?php echo $recipe->getNumberOfDishes() ?>"
    >
    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/minus.svg' ?>" alt="minus" class="minus-icon">
</div>
