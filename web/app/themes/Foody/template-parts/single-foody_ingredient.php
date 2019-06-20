<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/25/19
 * Time: 7:14 PM
 */

/** @var Foody_Ingredient $ingredient */
/** @noinspection PhpUndefinedVariableInspection */
$ingredient = $template_args['page'];

foody_get_template_part(
    get_template_directory() . '/template-parts/content-ingredient-display.php',
    [
        'page' => $ingredient
    ]
);