<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/29/18
 * Time: 2:56 PM
 */

add_filter('query_vars', 'add_vars');
function add_vars($public_query_vars)
{
    $public_query_vars[] = 'recipe';
    $public_query_vars[] = 'registered';
    $public_query_vars[] = Foody_Query::$page;
    return $public_query_vars;
}