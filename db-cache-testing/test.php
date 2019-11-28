<?php

require_once "../vendor/autoload.php";


define('WP_USE_THEMES', true);
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = $argv[1];
require_once '../web/wp/wp-load.php';


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
    test4();
    //test5();
    echo "\r\n";
    echo 'with_cache second run'. "\xA";
    test1();
    test2();
    test3();
    test4();
    //test5();
}


function test_query_without_cache()
{
    define('DONOTCACHEDB', true);
    //flush cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
    }
    echo 'without_cache'. "\xA";
    test1();
    test2();
    test3();
    test4();
    //test5();
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

function test4()
{
    $time_pre = microtime(true);
    $res = doQuery(4);
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;

    echo 'test 4 ' . " " . $exec_time . "\xA";
}

function test5()
{
    $time_pre = microtime(true);
    $res = doQuery(5);
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;

    echo 'test 5 ' . " " . $exec_time . "\xA";
}


function doQuery($num)
{
    global $wpdb;
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
        case 4:
            $query_args = array(
                'meta_key'   => '_yoast_wpseo_profile_updated',
                'orderby'    => 'meta_value_num',
                'order'      => 'DESC',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => $wpdb->get_blog_prefix() . 'user_level',
                        'value'   => '0',
                        'compare' => '!=',
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'key'     => 'wpseo_noindex_author',
                            'value'   => 'on',
                            'compare' => '!=',
                        ),
                        array(
                            'key'     => 'wpseo_noindex_author',
                            'compare' => 'NOT EXISTS',
                        ),
                    ),
                ),
            );
            return get_users($query_args);
            break;
        case 5:
            $query_args =  "SELECT count(post_id) FROM {$wpdb->postmeta} as postmeta
JOIN {$wpdb->posts} as posts
where posts.ID = postmeta.post_id
	AND meta_key like 'ingredients_ingredients_groups_%_ingredients_%_ingredient'
	AND meta_value = '960'
    AND post_status = 'publish'
group by post_id ";

            $results = $wpdb->get_results($query_args);
            if (empty($results)) {
                $amount = 0;
            } else {
                $amount = count($results);
            }
            return $amount;
            break;
    }

    $the_query = new WP_Query($query_args);
    return $the_query->posts;
}