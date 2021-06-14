<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */


/** @var Foody_Recipe $recipe */
/** @noinspection PhpUndefinedVariableInspection */
$recipe = $template_args['page'];

if ( in_category( 'עוגות', get_the_ID() ) ){
    foody_get_template_part(
        get_template_directory() . '/template-parts/content-recipe-display.php',
        [
            'recipe' => $recipe
        ]
    );
} else {
    foody_get_template_part(
        get_template_directory() . '/template-parts/content-recipe-display-old.php',
        [
            'recipe' => $recipe
        ]
    );
}
