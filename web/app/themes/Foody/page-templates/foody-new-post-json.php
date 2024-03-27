<?php
/**
 * Template Name: New Posts Json for Itay
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */
header("X-Robots-Tag: noindex, nofollow");

header('Content-Type: application/json; charset=utf-8');

// Get today's date and timestamp
 $today_year = date('Y');
 $today_month = date('m');
 $today_day = date('d');
 $after =  $today_year ."-". $today_month ."-".  ($today_day-1);
 $befor =  $today_year ."-". $today_month ."-".  ($today_day+1);
$rtn = array();
$freindlyUri = '';

// Arguments for get_posts
$args = array(
    'post_type' => array('foody_recipe', 'post'), // Replace with your actual custom post type name
    'post_status' => 'publish',
    'date_query' => array(
        array(
            'after' =>  $after,
            'before' =>  $befor ,
            'inclusive' => true,
        ),
    ),
    'numberposts' => -1, // Get all new posts
);

// Get all new foody_recipe posts
$foody_recipes = get_posts($args);
foreach ($foody_recipes as $k => $v) {
    $freindlyUri  = get_permalink($v->ID);
    $author_id = get_post_field( 'post_author', $v->ID );
    $author_info = get_userdata( $author_id );
    $feed_channal_ID = get_field("recipe_channel", $v->ID) ? get_field("recipe_channel", $v->ID) : '';
    $feed_channal_name = $feed_channal_ID ? get_the_title($feed_channal_ID) : '';
    //get_the_title

    $rtn[$k]['ID'] = $v->ID;
    $rtn[$k]['post_date'] = $v->post_date;
    $rtn[$k]['post_title'] = $v->post_title;
    $rtn[$k]['url'] = $freindlyUri;
    $rtn[$k]['post_type'] = $v->post_type;
    $rtn[$k]['Author_Name'] = $author_info->display_name;
    $rtn[$k]['Author_ID'] = $author_info->ID;
    $rtn[$k]['Feed_Channal_ID'] = $feed_channal_ID;
    $rtn[$k]['Feed_Channal_Name'] = $feed_channal_name;


//Getting main category of the post
$category_detail=get_the_category($v->ID);//$post->ID
        foreach($category_detail as $cd){
            $rtn[$k]['post_main_category_name'] = $cd->name;
            $rtn[$k]['post_main_category_id'] = $cd->term_id;
            
        }


}
print_r(json_encode($rtn));