<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/20/18
 * Time: 9:12 PM
 */

global $foody_page;

/** @var Foody_Playlist $playlist */
$playlist = $foody_page;

$recipe = $playlist->get_current_recipe();

foody_get_template_part(
    get_template_directory() . '/template-parts/content-recipe-display.php',
    [
        'recipe' => $recipe
    ]
)

?>