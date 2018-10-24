<?php
/**
 * Hooks and functions related
 * to WordPress menus, menu items
 * and related customizations.
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/28/18
 * Time: 4:52 PM
 */

/**
 * Used after the menu items are fetched
 * and sorted, and before rendered as html.
 *
 *
 * @param stdClass[] $sorted_menu_items menu items objects
 * @param stdClass $args menu args
 * @return array the menu items after handling
 * user roles based logic
 */
function user_role_menu_items_filter($sorted_menu_items, stdClass $args)
{

    if (!is_admin() && !empty($sorted_menu_items)) {
        $logged_in = is_user_logged_in();

        if ($logged_in) {
            $sorted_menu_items = array_filter($sorted_menu_items, function ($item) {
                $show_item_when_logged_in = get_field('show_when_logged_in', $item);
                return $show_item_when_logged_in;
            });
        } else {
            $sorted_menu_items = array_filter($sorted_menu_items, function ($item) {
                $show_item_only_logged_in = get_field('only_logged_in', $item);
                return $show_item_only_logged_in == false;
            });
        }
    }

    return $sorted_menu_items;
}

add_filter('wp_nav_menu_objects', 'user_role_menu_items_filter', 10, 2);



function mobile_only_menu_items_filter($sorted_menu_items, stdClass $args)
{

    if (!is_admin() && !empty($sorted_menu_items)) {
        $mobile = wp_is_mobile();

        if (!$mobile) {
            $sorted_menu_items = array_filter($sorted_menu_items, function ($item) {
                $mobile_only = get_field('mobile_only', $item);
                return $mobile_only == false;
            });
        }


    }

    return $sorted_menu_items;
}

add_filter('wp_nav_menu_objects', 'mobile_only_menu_items_filter', 10, 2);



function foody_menu_items_classes($sorted_menu_items, stdClass $args)
{

    if (!is_admin() && !empty($sorted_menu_items)) {

        foreach ($sorted_menu_items as $menu_item) {
            $mobile = get_field('mobile_only', $menu_item);
            if ($mobile) {
                $menu_item->classes[] = 'foody-mobile-menu-item';
            }
        }
    }

    return $sorted_menu_items;
}

add_filter('wp_nav_menu_objects', 'foody_menu_items_classes', 10, 2);



/**
 * @param stdClass[] $sorted_menu_items menu items objects
 * @param stdClass $args menu args
 * @return array the menu items after adding
 * dynamic items
 */
function add_dynamic_menu_items($sorted_menu_items, $args)
{
    if (
        !is_admin() &&
        $args->theme_location == 'primary' &&
        !is_user_logged_in()
    ) {
        $login_title = __('התחבר', 'foody');

        $login_item = (object)[
            'ID' => PHP_INT_MAX,
            'db_id' => PHP_INT_MAX,
            'title' => $login_title,
            'url' => wp_login_url(),
            'attr_title' => $login_title,
            'target' => '',
            'xfn' => '',
            'object' => null
        ];

        $signup_title = __('הירשם', 'foody');

        $signup_item = (object)[
            'ID' => PHP_INT_MAX - 1,
            'db_id' => PHP_INT_MAX - 1,
            'title' => $signup_title,
            'url' => get_permalink(get_page_by_path('הרשמה')),
            'attr_title' => $signup_title,
            'target' => '',
            'xfn' => '',
            'object' => null
        ];

        $inline_children = [
            $login_item,
            $signup_item
        ];

        $start_id = intval($args->menu->menu_id . strval(count($sorted_menu_items) + 1));

        $items_to_add = foody_get_menu_item_with_inline_children($start_id, $inline_children);

        $sorted_menu_items = array_merge($sorted_menu_items, $items_to_add);
    }elseif(  $args->theme_location == 'primary' &&
        is_user_logged_in()){

        $logout_item = (object)[
            'ID' => PHP_INT_MAX - 1,
            'db_id' => PHP_INT_MAX - 1,
            'title' => __('התנתק'),
            'url' => wp_logout_url(),
            'attr_title' => __('התנתק'),
            'target' => '',
            'xfn' => '',
            'object' => null
        ];

        $sorted_menu_items[]= $logout_item;
    }

    if ($args->theme_location == 'primary' && wp_is_mobile()) {

        $menu_item = new stdClass();
        $menu_item->ID = PHP_INT_MAX;
        $menu_item->url = '';
        $menu_item->title = '<div class=""> <span>' . __('תפריט', 'foody') . '</span><span class="close" data-toggle="collapse" data-target="#foody-navbar-collapse"
                        aria-controls="foody-navbar-collapse">&times;</span> </div>';
        $menu_item->attr_title = '';
        $menu_item->target = '';
        $menu_item->xfn = '';
        $menu_item->menu_order = PHP_INT_MAX;
        $menu_item->object_id = PHP_INT_MAX;
        $menu_item->db_id = PHP_INT_MAX;
        $menu_item->object = null;
        $menu_item->after = '<span>&times;</span>';
        $classes = 'close-menu foody-mobile-menu-item';

        $menu_item->classes = $classes;

        array_unshift($sorted_menu_items, $menu_item);
    }

    return $sorted_menu_items;
}

add_filter('wp_nav_menu_objects', 'add_dynamic_menu_items', 10, 2);
function foody_get_menu_item_with_inline_children($incremental_id, $children)
{
    $inline_children = new stdClass();

    $inline_children->separator = '/';

    foreach ($children as $child) {
        $new_item = new stdClass;
        $new_item->ID = $incremental_id;
        $new_item->url = $child->url;
        $new_item->title = $child->title;
        $new_item->attr_title = $child->title;
        $new_item->target = '';
        $new_item->xfn = '';
        $new_item->menu_order = PHP_INT_MAX;
        $new_item->object_id = $incremental_id;
        $new_item->db_id = $incremental_id;
        $new_item->object = null;
        $classes = 'menu-item';
        $classes .= ' menu-item-type-post_type';
        $classes .= ' menu-item-object-page';
        $new_item->classes = $classes;
        $new_item->link_classes = ['nav-link-inline'];
        $inline_children->children[] = $new_item;
        $incremental_id++;
    }

    $menu_item = new stdClass();
    $menu_item->ID = $incremental_id;
    $menu_item->url = '';
    $menu_item->title = '';
    $menu_item->attr_title = '';
    $menu_item->target = '';
    $menu_item->xfn = '';
    $menu_item->menu_order = PHP_INT_MAX;
    $menu_item->object = null;
    $menu_item->object_id = $incremental_id;
    $menu_item->db_id = $incremental_id;

    $classes = 'menu-item login-signup';
    $classes .= ' menu-item-type-post_type';
    $classes .= ' menu-item-object-page';

    $menu_item->classes = $classes;

    $menu_item->inline_children = $inline_children;


    return [$menu_item];
}


function add_menu_items($items_html, $args)
{

    // FEATURE maybe add profile avatar and tv menu here

    return $items_html;
}


add_filter('wp_nav_menu_items', 'add_menu_items', 10, 2);