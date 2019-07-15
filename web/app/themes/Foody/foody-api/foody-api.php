<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/26/19
 * Time: 5:04 PM
 */

$api_dir = '/foody-api/';

require_once get_template_directory() . $api_dir . '/functions/foody-api.php';
require_once get_template_directory() . $api_dir . '/inc/inc.php';


function foody_bot_register_endpoints()
{
    $bot_controller = new \FoodyAPI\Foody_BotAPIController();
    $bot_controller->registerRoutes();
}

add_action('rest_api_init', 'foody_bot_register_endpoints');