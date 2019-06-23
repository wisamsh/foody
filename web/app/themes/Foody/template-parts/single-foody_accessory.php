<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/25/19
 * Time: 7:14 PM
 */

/** @var Foody_Accessory $accessory */
/** @noinspection PhpUndefinedVariableInspection */
$accessory = $template_args['page'];

foody_get_template_part(
    get_template_directory() . '/template-parts/content-accessory-display.php',
    [
        'page' => $accessory
    ]
);