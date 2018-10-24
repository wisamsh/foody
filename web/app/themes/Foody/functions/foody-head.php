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


function get_page_type()
{

    $type = 'article';

    if (is_home() || is_front_page()) {
        $type = 'home';
    } elseif (is_category()) {
        $type = 'category';
    } elseif (is_search()) {
        $type = 'search';
    } elseif (is_author()) {
        $type = 'author';
    } elseif (is_single()) {
        $post_type = get_post_type();

        if (!empty($post_type) && $post_type != 'post') {
            $type = str_replace('foody_', '', $post_type);
        }
    } elseif (is_page_template('page-templates/categories.php')) {

        $type = 'categories';

    } elseif (is_page_template('page-templates/profile.php')) {

        $type = 'profile';

    } elseif (is_page('הנבחרת שלנו')) {

        $type = 'team';

    }

    return $type;
}


function foody_js_globals_main($vars)
{

    $vars['queryPage'] = Foody_Query::$page;
    $vars['objectID'] = get_queried_object_id();
    $vars['title'] = get_the_title();
    $vars['type'] = get_page_type();
    $vars['mixpanelToken'] = MIXPANEL_TOKEN;


    if (is_single()) {

        $vars['post'] = [
            'ID' => get_the_ID(),
            'type' => get_post_type(),
            'title' => get_the_title()
        ];
    }


    return $vars;
}

add_filter('foody_js_globals', 'foody_js_globals_main');


function is_tablet($vars)
{
    $tablet_browser = foody_is_tablet();

    $vars['isTablet'] = $tablet_browser;
    return $vars;
}

add_filter('foody_js_globals', 'is_tablet');
