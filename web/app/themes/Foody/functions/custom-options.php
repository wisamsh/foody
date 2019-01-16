<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/5/18
 * Time: 11:51 AM
 */

register_setting('discussion','hid_per_page');


$page_name_search_options = __('הגדרות חיפוש - פודי', 'foody');
$page_name_purchase_buttons = __('כפתורי רכישה', 'foody');

/** @var array $options_pages
 * All ACF Options Pages.
 * page_title,menu_title and menu_slug
 * are required.
 * If post_id is not set, default will be 'options'
 */
$options_pages = array(
    array(
        'page_title' => $page_name_search_options,
        'menu_title' => $page_name_search_options,
        'menu_slug' => 'foody-search-options.php',
        'post_id' => 'foody_search_options',
        'icon_url' => 'dashicons-search'
    ),
    array(
        'page_title' => $page_name_purchase_buttons,
        'menu_title' => $page_name_purchase_buttons,
        'menu_slug' => 'foody-purchase-options.php',
        'post_id' => 'foody_purchase_options',
        'icon_url' => 'dashicons-cart'
    )
);


/** @var array $default_args
 * default arguments to @see acf_add_options_page()
 * Will be merged with the specific arguments
 * set in @see $options_pages
 */
$default_args = array(
    /* (int|string) The position in the menu order this menu should appear.
    WARNING: if two menu items use the same position attribute, one of the items may be overwritten so that only one item displays!
    Risk of conflict can be reduced by using decimal instead of integer values, e.g. '63.3' instead of 63 (must use quotes).
    Defaults to bottom of utility menu items */
    'position' => false,

    /* (string) The slug of another WP admin page. if set, this will become a child page. */
    'parent_slug' => '',

    /* (string) The icon class for this menu. Defaults to default WordPress gear.
    Read more about dashicons here: https://developer.wordpress.org/resource/dashicons/ */
    'icon_url' => false,

    /* (boolean) If set to true, this options page will redirect to the first child page (if a child page exists).
    If set to false, this parent page will appear alongside any child pages. Defaults to true */
    'redirect' => true,

    /* (int|string) The '$post_id' to save/load data to/from. Can be set to a numeric post ID (123), or a string ('user_2').
    Defaults to 'options'. Added in v5.2.7 */
    'post_id' => 'options',

    /* (boolean)  Whether to load the option (values saved from this options page) when WordPress starts up.
    Defaults to false. Added in v5.2.8. */
    'autoload' => false,

    /* (string) The update button text. Added in v5.3.7. */
    'update_button' => __('Update', 'acf'),

    /* (string) The message shown above the form on submit. Added in v5.6.0. */
    'updated_message' => __("Options Updated", 'acf'),

);


if (function_exists('acf_add_options_page')) {
    foreach ($options_pages as $options_page_args) {
        if (validate_args($options_page_args)) {
            $args = array_merge($default_args, $options_page_args);
            acf_add_options_page($args);
        }
    }
}


/**
 * Validates arguments for @see acf_add_options_page()
 *
 * @param $args array specific arguments to a page
 * @return bool true if required arguments are set
 */
function validate_args($args)
{
    $valid = false;
    if ($args != null) {
        if (isset($args['page_title']) && isset($args['menu_title']) && isset($args['menu_slug'])) {
            $valid = true;
        }
    }

    return $valid;
}


function custom_options()
{

    register_setting('discussion', 'hid_per_page');

    add_settings_field('hid_per_page', __('מספר ״איך יצא לי״ בעמוד'), 'foody_custom_options_callback', 'discussion');

    function foody_custom_options_callback()
    {

        $options = get_option('hid_per_page', 3);

        echo '<input type="number" id="hid_per_page" name="hid_per_page" value="' . $options . '"></input>';

    }
}

add_action('admin_init', 'custom_options');
