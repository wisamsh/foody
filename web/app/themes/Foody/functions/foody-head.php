<?php
/**
 * This functions file include head
 * modifications and actions (most probably usage of the wp_head action hook).
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 11:15 PM
 */

add_action('wp_head', 'Foody_Header::facebook_init');


add_filter('foody_js_globals', function ($vars) {

    $vars['queryPage'] = Foody_Query::$page;
    $vars['objectID'] = get_queried_object_id();


    return $vars;
});
