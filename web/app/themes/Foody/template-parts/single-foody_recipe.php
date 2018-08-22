<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

global $foody_page;

/** @var Foody_Recipe $recipe */
$recipe = $foody_page;

foody_get_template_part(
    get_template_directory() . '/template-parts/content-recipe-display.php',
    [
        'recipe' => $recipe
    ]
)

?>
