<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/24/19
 * Time: 4:43 PM
 */


/** @var Foody_Feed_Channel $feed_channel */
/** @noinspection PhpUndefinedVariableInspection */
$feed_channel = $template_args['page'];

foody_get_template_part(
    get_template_directory() . '/template-parts/content-feed-channel-display.php',
    [
        'page' => $feed_channel
    ]
);