<?php

require_once "../vendor/autoload.php";


define('WP_USE_THEMES', true);
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = $argv[1];
require_once '../web/wp/wp-load.php';
global $wpdb;

test_query_with_cache();
echo "\r\n";
test_query_without_cache();
echo "\r\n";


function test_query_with_cache()
{
    echo 'with_cache first run'. "\xA";
    test1();
    test2();
    test3();
    echo "\r\n";
    echo 'with_cache second run'. "\xA";
    test1();
    test2();
    test3();
}


function test_query_without_cache()
{
    define('DONOTCACHEDB', true);
    //flush cache
    if (function_exists('w3tc_flush_posts')) {
        w3tc_flush_posts();
    }
    echo 'without_cache'. "\xA";
    test1();
    test2();
    test3();
}

function test1()
{
    $time_pre = microtime(true);
    $res = doQuery(1);
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;

    echo 'test 1 ' . " " . $exec_time . "\xA";
}

function test2()
{
    $time_pre = microtime(true);
    $res = doQuery(2);
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;

    echo 'test 2 ' . " " . $exec_time . "\xA";
//    echo __($res[0]->post_title) . "\xA";
}

function test3()
{
    $time_pre = microtime(true);
    $res = doQuery(3);
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;

    echo 'test 3 ' . " " . $exec_time . "\xA";
}


function doQuery($num)
{
    switch ($num) {
        case 1:
            $query_args = array(
                'post_type' => 'foody_recipe',
                'posts_per_page' => -1,
                'order' => 'ASC',
                'orderby' => 'rand',
                'meta_query' => [
                    [
                        'key' => '_yoast_wpseo_primary_category',
                        'compare' => 'IN',
                        'value' => ['6', '5'],
                        'type' => 'NUMERIC'
                    ]
                ]
            );
            break;
        case 2:
            $query_args = array(
                'post_type' => 'foody_recipe',
                'p' => '21293'
            );
            break;
        case 3:
            $query_args = [
                'post_type' => 'foody_recipe',
                'post_status' => 'publish',
                'posts_per_page' => 5,
                'category__and' => [6]
            ];
            break;
    }

    $the_query = new WP_Query($query_args);
    return $the_query->posts;
}