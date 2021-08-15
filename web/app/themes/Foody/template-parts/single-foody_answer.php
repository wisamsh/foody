<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */


/** @var Foody_Answer $answer */
/** @noinspection PhpUndefinedVariableInspection */
$answer = $template_args['page'];

foody_get_template_part(
    get_template_directory() . '/template-parts/content-faq-display.php',
    [
        'answer' => $answer
    ]
);