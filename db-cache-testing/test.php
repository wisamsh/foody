<?php

define('WP_USE_THEMES', true);

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'foody-local.co.il';
require_once '../web/wp/wp-load.php';
global $wpdb;

test_query_with_cache();
echo "\r\n";
test_query_without_cache();
echo "\r\n";


function test_query_with_cache(){
    $time_pre = microtime(true);
    $res = doQuery();
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;

    echo 'with_cache'." ".$exec_time;
}

function test_query_without_cache(){
    define('DONOTCACHEDB', true);
    $time_pre = microtime(true);
    $res = doQuery();
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;

    echo 'without_cache'." ".$exec_time;
}

function doQuery(){
    $query_args = array(
        'post_type' => 'foody_recipe',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'rand',
        'meta_query' => [
            [
                'key' => '_yoast_wpseo_primary_category',
                'compare' => 'IN',
                'value' => ['6','5'],
                'type' => 'NUMERIC'
            ]
        ]
    );

    $the_query = new WP_Query($query_args);
    return $the_query->posts;
}