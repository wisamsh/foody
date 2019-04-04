<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/24/19
 * Time: 4:46 PM
 */
/** @var Foody_Feed_Channel $feed_channel */
/** @noinspection PhpUndefinedVariableInspection */
$feed_channel = $template_args['page'];

$feed_channel->blocks();

$feed_channel->the_css();