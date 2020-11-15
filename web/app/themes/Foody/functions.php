<?php
/**
 * Foody functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Foody
 */


if (!function_exists('foody_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function foody_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Foody, use a find and replace
         * to change 'foody' to the name of your theme in all the template files.
         */
//        load_theme_textdomain('foody', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'menu-1' => esc_html__('Primary', 'foody'),
            'footer-pages' => esc_html__('Footer Pages', 'foody'),
            'footer-links' => esc_html__('Footer Links', 'foody'),
            'channels-menu' => esc_html__('Channels Menu', 'foody')
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('foody_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        )));

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        add_theme_support('custom-logo', array(
            'height' => 250,
            'width' => 250,
            'flex-width' => true,
            'flex-height' => true,
        ));
    }
endif;
add_action('after_setup_theme', 'foody_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function foody_content_width()
{
    // This variable is intended to be overruled from themes.
    // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $GLOBALS['content_width'] = apply_filters('foody_content_width', 640);
}

add_action('after_setup_theme', 'foody_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function foody_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'foody'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'foody'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}

add_action('widgets_init', 'foody_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function foody_scripts()
{
    $added_login = false;

    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js', array(), null, false);

    wp_enqueue_script('foody-navigation', get_template_directory_uri() . '/resources/js/navigation.js', array(), '20151215', true);

    wp_enqueue_script('foody-skip-link-focus-fix', get_template_directory_uri() . '/resources/js/skip-link-focus-fix.js', array(), '20151215', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply', false, false, true, true);
    }

    if (!is_admin()) {

        $style = foody_get_versioned_asset('style');
        wp_enqueue_script('foody-style', $style, false, false, true);

        $post_content = '';
        $current_post = get_post();

        if (!empty($current_post)) {
            $post_content = $current_post->post_content;
        }

        $lazy_asset = foody_get_versioned_asset('lazy');
        wp_enqueue_script('foody-script-lazy', $lazy_asset, false, false, false);


        // Homepage
        if (is_front_page() || is_home() || is_404()) {
            $homepage_asset = foody_get_versioned_asset('homepage');
            wp_enqueue_script('foody-script-home', $homepage_asset, false, false, true);
        }

        if (is_page_template('page-templates/profile.php')) {
            $profile_asset = foody_get_versioned_asset('profile');
            wp_enqueue_script('foody-script-profile', $profile_asset, false, false, true);
        }

        if (has_shortcode($post_content, 'foody-approvals') || is_page_template('page-templates/foody-campaign.php') || is_page_template('page-templates/foody-campaign-extended.php')) {
            $campaign_asset = foody_get_versioned_asset('campaign');
            wp_enqueue_script('foody-script-campaign', $campaign_asset, false, false, true);
        }

        if (is_category()) {
            $categories_asset = foody_get_versioned_asset('categories');
            wp_enqueue_script('foody-script-categories', $categories_asset, false, false, true);
        }

        if (is_page_template('page-templates/channel.php')) {
            $channel_asset = foody_get_versioned_asset('channel');
            wp_enqueue_script('foody-script-channel', $channel_asset, false, false, true);
        }

        if (
            (is_page_template('page-templates/centered-content.php') && !has_shortcode($post_content, 'foody_team')) ||
            is_page_template('page-templates/categories.php') ||
            has_shortcode($post_content, 'contact-form-7')
        ) {
            $general_asset = foody_get_versioned_asset('general');
            wp_enqueue_script('foody-script-general', $general_asset, false, false, true);
            if (is_page_template('page-templates/categories.php')) {
                $categories_asset = foody_get_versioned_asset('categories');
                wp_enqueue_script('foody-script-categories', $categories_asset, false, false, true);
            }
        }

        if (is_search()) {
            $search_results_asset = foody_get_versioned_asset('searchResults');
            wp_enqueue_script('foody-script-search-results', $search_results_asset, false, false, true);
        }

        if (is_page_template('page-templates/content-with-sidebar.php') && is_single()
            && !in_array(get_post_type(), ['foody_ingredient', 'foody_accessory', 'foody_technique', 'foody_feed_channel'])) {
            $post_asset = foody_get_versioned_asset('post');
            wp_enqueue_script('foody-script-recipe', $post_asset, false, false, true);

        }

        if (is_page_template('page-templates/content-with-sidebar.php') && is_single()
            && in_array(get_post_type(), ['foody_feed_channel'])) {
            $feed_channel_asset = foody_get_versioned_asset('feedChannel');
            wp_enqueue_script('foody-script-feed-channel', $feed_channel_asset, false, false, true);
        }

        if (is_page_template('page-templates/foody-course.php')) {
            $course_asset = foody_get_versioned_asset('course');
            wp_enqueue_script('foody-script-course', $course_asset, false, false, true);
        }

        if (is_page_template('page-templates/foody-course-new.php')) {
            $course_v2_asset = foody_get_versioned_asset('coursev2');
            wp_enqueue_script('foody-script-course-v2', $course_v2_asset, false, false, true);
        }

        if (is_page_template('page-templates/foody-courses-homepage.php')) {
            $courses_homepage_asset = foody_get_versioned_asset('courseshomepage');
            wp_enqueue_script('foody-script-courses-homepage', $courses_homepage_asset, false, false, true);
        }

        if (is_page_template('page-templates/foody-course-register.php') || is_page_template('page-templates/foody-courses-thank-you.php')) {
            $courses_register_asset = foody_get_versioned_asset('courseregister');
            wp_enqueue_script('foody-script-course-register', $courses_register_asset, false, false, true);
        }

        if (is_page_template('page-templates/foody-course-efrat.php')) {
            $course_asset = foody_get_versioned_asset('course');
            wp_enqueue_script('foody-script-course', $course_asset, false, false, true);
        }

        if (is_page_template('page-templates/foody-course-new.php')) {
            $course_asset = foody_get_versioned_asset('course');
            wp_enqueue_script('foody-script-course', $course_asset, false, false, true);
        }

        if (is_page_template('page-templates/items.php')) {
            $items_asset = foody_get_versioned_asset('items');
            wp_enqueue_script('foody-script-recipe', $items_asset, false, false, true);
        }

        if (has_shortcode($post_content, 'foody-login')) {
            $login_asset = foody_get_versioned_asset('login');
            wp_enqueue_script('foody-script-login', $login_asset, false, false, true);
        }

//        if (!is_user_logged_in() && (!function_exists('foody_is_registration_open') || foody_is_registration_open()) && !$added_login) {
//            $login_asset = foody_get_versioned_asset('login');
//            wp_enqueue_script('foody-script-login', $login_asset, false, false, true);
//        }

        if (has_shortcode($post_content, 'foody-register')) {
            $register_asset = foody_get_versioned_asset('register');
            wp_enqueue_script('foody-script-register', $register_asset, false, false, true);
            wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js', false, false, true);
        }

        if (is_author()) {
            $author_asset = foody_get_versioned_asset('author');
            wp_enqueue_script('foody-script-author', $author_asset, false, false, true);
        }

        if (is_tag() || in_array(get_post_type(), ['foody_ingredient', 'foody_accessory', 'foody_technique'])) {
            $tag_asset = foody_get_versioned_asset('tag');
            wp_enqueue_script('foody-script-tag', $tag_asset, false, false, true);
        }

        if (has_shortcode($post_content, 'foody_team')) {
            $team_asset = foody_get_versioned_asset('team');
            wp_enqueue_script('foody-script-team', $team_asset, false, false, true);
        }

        if (get_post_type() == 'foody_playlist') {
            $playlist_asset = foody_get_versioned_asset('playlist');
            wp_enqueue_script('foody-script-plalist', $playlist_asset, false, false, true);
        }
    }


}

add_action('wp_enqueue_scripts', 'foody_scripts');

//function foody_add_async_script( $url ) {
//	if ( strpos( $url, '#asyncload' ) === false ) {
//		return $url;
//	} else if ( is_admin() ) {
//		return str_replace( '#asyncload', '', $url );
//	} else {
//		return str_replace( '#asyncload', '', $url ) . "' async='async";
//	}
//}
//
//add_filter( 'clean_url', 'foody_add_async_script', 11, 1 );

function foody_add_footer_styles()
{

    wp_register_style('sb_instagram_styles', content_url('/plugins/instagram-feed/css/sb-instagram.min.css'), array(), SBIVER);
    wp_enqueue_style('sb_instagram_styles');

    if (is_single()) {
        wp_register_style('wp-postratings', content_url('/plugins/wp-postratings/css/postratings-css.css'), false, WP_POSTRATINGS_VERSION, 'all');
        wp_enqueue_style('wp-postratings');

        wp_register_style('wp-postratings-rtl', content_url('/plugins/wp-postratings/css/postratings-css-rtl.css'), false, WP_POSTRATINGS_VERSION, 'all');
        wp_enqueue_style('wp-postratings-rtl');
    }
}

add_action('get_footer', 'foody_add_footer_styles', 10000000000000000);

add_action('wp_print_styles', 'my_deregister_styles', 100000000000);

function my_deregister_styles()
{
    wp_deregister_style('dashicons');
    wp_dequeue_style('fontawesome');
    wp_deregister_style('fontawesome');
}

function foody_custom_dequeue()
{

    wp_deregister_style('sb_instagram_styles');
    wp_dequeue_style('sb_instagram_styles');

    wp_deregister_style('wp-postratings');
    wp_dequeue_style('wp-postratings');
    wp_deregister_style('wp-postratings-rtl');
    wp_dequeue_style('wp-postratings-rtl');

    global $wp_styles;

    foreach ($wp_styles->registered as $handle => $args) {
        if ($handle != 'easy-social-share-buttons') {
            if (preg_match('/^essb-/', $handle)) {
                wp_deregister_style($handle);
                wp_dequeue_style($handle);
            }
        }
    }

}

add_action('wp_print_styles', 'foody_custom_dequeue', 9999);
add_action('wp_enqueue_scripts', 'foody_custom_dequeue', 9999);
add_action('wp_head', 'foody_custom_dequeue', 9999);

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';


register_nav_menus(array(
    'primary' => __('Primary Menu', 'foody'),
));

require_once get_template_directory() . '/functions/includes.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}


/**
 * Load Foody background processes
 */
require get_template_directory() . '/foody-background-processes/foody-bp-content-sync.php';


function admin_theme_style()
{
    $asset = foody_get_versioned_asset('admin');
    wp_enqueue_script('admin-script', $asset, false, false, true);
}


add_action('admin_enqueue_scripts', 'admin_theme_style');
add_action('login_enqueue_scripts', 'admin_theme_style');


function foody_get_versioned_asset($name)
{
    $assets_version = file_get_contents(get_template_directory() . '/build/version-hash.txt');

    return get_template_directory_uri() . "/dist/$name.$assets_version.js#asyncload";

}

function add_async_attribute($tag, $handle)
{
    $scripts_to_defer = array(
        'foody-script-home',
        'foody-script-profile',
        'foody-script-campaign',
        'foody-script-centered_content',
        'foody-script-categories',
        'foody-script-recipe',
        'foody-script-login',
        'foody-script-register',
        'foody-script-author',
        'foody-script-tag',
        'foody-script-team',
        'foody-script-search-results',
        'foody-script-feed-channel',
        'foody-script-plalist',
        'foody-white-label',
        'sb_instagram_scripts',
        'ui-a11y.js',
        'wsl-widget'
    );
    foreach ($scripts_to_defer as $defer_script) {
        if ($defer_script === $handle) {
            return str_replace(' src', ' async defer src', $tag);
        }
    }

    return $tag;
}

//add_filter( 'script_loader_tag', 'add_async_attribute', 10, 2 );

function essb_stylebuilder_css_filess()
{

}

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

add_action('wp_print_styles', 'wps_deregister_styles', 100);
function wps_deregister_styles()
{
    wp_deregister_style('contact-form-7');
}

// The callback function for the action hook bellow
function any_script_in_footer()
{
    if (!is_admin()) {
        // Call the list with all the registered scripts
        global $wp_scripts;

        if (isset ($wp_scripts->registered) && !empty ($wp_scripts->registered) && is_array($wp_scripts->registered)) {
            foreach ($wp_scripts->registered as $idx => $script) {
                if (isset($wp_scripts->registered[$idx]->extra) && is_array($wp_scripts->registered[$idx]->extra)) {

                    // Set any of the scripts to belong in the footer group
                    $wp_scripts->registered[$idx]->extra['group'] = 1;
                }
            }
        }
    }
}

// Call the callback function with the `wp_print_scripts` hook at the very end
// of the callbacks cue
//add_action('wp_print_scripts', 'any_script_in_footer', 10000000000000);


function hide_w3tc()
{
    if (defined('WP_ENV') && WP_ENV == 'production') {
        remove_menu_page('w3tc_dashboard');
    }
}

add_action('network_admin_menu', 'hide_w3tc', 111);
add_action('admin_menu', 'hide_w3tc', 111);


add_action('admin_bar_menu', 'foody_remove_from_admin_bar', 999);
/**
 * @param $wp_admin_bar WP_Admin_Bar
 */
function foody_remove_from_admin_bar($wp_admin_bar)
{
    if (defined('WP_ENV') && WP_ENV == 'production') {
        if (!current_user_can('manage_network')) {
            $wp_admin_bar->remove_node('w3tc');
        } else {
            $wp_admin_bar->remove_node('w3tc_settings_general');
            $wp_admin_bar->remove_node('w3tc_settings_extensions');
            $wp_admin_bar->remove_node('w3tc_settings_general');
        }
    }

}

add_filter('manage_foody_ingredient_posts_columns', 'set_custom_edit_foody_ingredient_columns');
function set_custom_edit_foody_ingredient_columns($columns)
{
    $columns['recipes'] = __('מתכונים', 'your_text_domain');

    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action('manage_foody_ingredient_posts_custom_column', 'custom_foody_ingredient_column', 10, 2);
function custom_foody_ingredient_column($column, $post_id)
{
    switch ($column) {
        case 'recipes' :
            $current_site_url = get_site(get_current_blog_id())->domain;
            echo '<a href=http://' . $current_site_url . '/wp/wp-admin/edit.php?post_type=foody_ingredient&page=ingredients_recipes_list&ingredient_id=' . $post_id . '>' . __('למתכונים') . '</a>';
            break;

    }
}

//* Add CSS directly into the admin head
add_action('admin_head', 'foody_custom_wp_admin_style_head');
function foody_custom_wp_admin_style_head()
{ ?>
    <style>
        #wpfooter {
            position: static
        }
    </style>
<?php }

function ingredients_recipes_list_options()
{
    add_submenu_page('edit.php?post_type=foody_ingredient', 'recipes', null, 'administrator', 'ingredients_recipes_list', 'ingredients_recipes_list_adjustments', 20);
}

add_action('admin_menu', 'ingredients_recipes_list_options');

function ingredients_recipes_list_adjustments()
{
    $ingredient_id = $_GET['ingredient_id'];
    $myListTable = new Ingredient_List_Table($ingredient_id);
    //echo '</pre><div class="wrap"><h2>Taxis Table</h2>';
    $myListTable->prepare_items();
    ?>
    <form method="post">
        <input type="hidden" name="page" value="test_list_table">
    <?php
    //$myListTable->search_box( 'search', 'search_id' );
    $myListTable->display();
    echo '</form></div>';
}

function ingredients_delta_export_menu_options()
{
    add_submenu_page('edit.php?post_type=foody_ingredient', 'export new ingredients', __('ייצא מצרכים חדשים'), 'administrator', 'new_ingredients_export', 'ingredients_export_adjustments', 19);
}

add_action('admin_menu', 'ingredients_delta_export_menu_options');


function ingredients_export_adjustments()
{
    Foody_ingredients_exporter::generate_xlsx();
}

function update_filters_cache_menu_options()
{
    $current_blog_id = get_current_blog_id();
    $switched = false;

    if ($current_blog_id != 1) {
        switch_to_blog(1);
        $switched = true;
    }

    $user_id = get_current_user_id();
    $user_meta = get_userdata($user_id);

    if ($switched) {
        switch_to_blog($current_blog_id);
    }

    if (is_array($user_meta->roles) && in_array('administrator', $user_meta->roles)) {
        add_submenu_page('edit.php?post_type=foody_filter', 'update filters cache', __('עדכן cache של פילטרים'), 'administrator', 'update_filters_cache', 'foody_update_filters_cache', 19);
    }
}

add_action('admin_menu', 'update_filters_cache_menu_options');

function foody_update_filters_cache()
{
    update_filters_cache();
    echo '<h1>' . __("ה - cache של הפילטרים עודכן בהצלחה") . '</h1>';
}

//function rss_post_thumbnail($content) {
//    global $post;
//    if(has_post_thumbnail($post->ID)) {
//        $content = '<p>' . get_the_post_thumbnail($post->ID) .
//            '</p>' . get_the_content();
//    }
//    return $content;
//}
//add_filter('the_excerpt_rss', 'rss_post_thumbnail');
//add_filter('the_content_feed', 'rss_post_thumbnail');


function init_rss()
{
    add_feed('custom-rss', 'custom_rss_feed');
}

add_action('init', 'init_rss');


function custom_rss_feed()
{
    get_template_part('foody-custom-rss.php', 'custom-rss');
}

function rss_campaign_tracking($post_permalink)
{
    $maariv_RSS_slugs = ['/maariv-rss/', '/maariv2-rss/'];
    if (isset($GLOBALS['path']) && in_array($GLOBALS['path'], $maariv_RSS_slugs)) {
        return $post_permalink . '?utm_source=Maariv%20site&utm_medium=promos&utm_campaign=maariv%20food';
    } else {
        return $post_permalink;
    }
}

;
add_filter('the_permalink_rss', 'rss_campaign_tracking');


add_filter('posts_orderby', 'order_search_by_posttype', 10, 2);
function order_search_by_posttype($orderby, $wp_query)
{
    if ((isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'acf-field') || (isset($_REQUEST['sort']) && $_REQUEST['sort'] != '')) {
        return $orderby;
    }
    if ($wp_query->is_search ||
        (!empty($_POST) && ((isset($_POST['action']) && $_POST['action'] == 'load_more' && (isset($_POST['context']) && $_POST['context'] != 'category')) ||
                (isset($_POST['action']) && $_POST['action'] == 'foody_filter' && (isset($_POST['data']) && (isset($_POST['data']['context']) && $_POST['data']['context'] != "category")))))) :
        global $wpdb;
        $orderby =
            "
            CASE WHEN {$wpdb->prefix}posts.post_type = 'foody_feed_channel' THEN '1'
            ELSE {$wpdb->prefix}posts.post_type END ASC,
            {$wpdb->prefix}posts.post_date DESC";
    endif;
    return $orderby;
}

// WHEN {$wpdb->prefix}posts.post_type = 'foody_recipe' THEN '2'
//                 WHEN {$wpdb->prefix}posts.post_type = 'post' THEN '3'
//

function my_wp_is_mobile()
{
    static $is_mobile;

    if (isset($is_mobile))
        return $is_mobile;

    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        $is_mobile = false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false) {
        $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'Tablet') == false) {
        $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Tablet') !== false) {
        $is_mobile = false;
    } else {
        $is_mobile = false;
    }

    return $is_mobile;
}

function foody_is_ios()
{
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        //Detect special conditions devices
        $iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $safari = strpos($_SERVER['HTTP_USER_AGENT'], 'Safari');

        return $iPod || $iPhone || $iPad || $safari;
    }
    return false;
}

add_action('init', 'register_update_filter_cache');
function register_update_filter_cache()
{
    if (defined('FOODY_FILTERS_CACHE') && FOODY_FILTERS_CACHE) {
        // Make sure this event hasn't been scheduled
        if (!wp_next_scheduled('foody_update_filters_cache_hook')) {
            // Schedule the event
            wp_schedule_event(strtotime('21:00:00'), 'daily', 'foody_update_filters_cache_hook');
        }
    }
}

add_filter('cron_schedules', 'foody_add_cron_interval');
function foody_add_cron_interval($schedules)
{
    if (!isset($schedules['one_minute'])) {
        $schedules['one_minute'] = array(
            'interval' => 60,
            'display' => esc_html__('Every Minute'),);
    }
    return $schedules;
}


function foody_add_update_authors_list_settings_tab()
{
    add_submenu_page('users.php', null, __('עדכון רשימת היוצרים'), 'administrator', 'foody_update_authors_list', 'foody_update_authors_list_func', 20);
}

add_action('admin_menu', 'foody_add_update_authors_list_settings_tab');


function foody_update_authors_list_func()
{
    if (is_admin() && is_user_logged_in() && current_user_can('administrator')) {
        global $wpdb;
        $new_table_name = $wpdb->prefix . 'foody_authors_names';
        $users_table = $wpdb->prefix . 'users';
        $usermeta_table = $wpdb->prefix . 'usermeta';
        $last_full_name = '';
        $last_full_name_reversed = '';
        $insert_query = "INSERT INTO {$new_table_name} (`author_id`, `first_name`, `last_name`, `full_name`, `reversed_full_name`) VALUES ";

        $fetch_authors_query = "SELECT user_id, meta_key, meta_value FROM {$usermeta_table}
where user_id IN (SELECT ID FROM {$users_table} as users
join {$usermeta_table} as usersmeta
on users.ID = usersmeta.user_id 
where usersmeta.meta_key = 'wp_capabilities' and usersmeta.meta_value = 'a:1:{s:6:\"author\";b:1;}')
and (meta_key ='first_name' or meta_key = 'last_name')";

        $authors_results = $wpdb->get_results($fetch_authors_query);

        foreach ($authors_results as $index => $authors_result) {
            if ($authors_result->meta_key == 'first_name') {
                $first_name = strpos($authors_result->meta_value, "'") != false ? str_replace(["'", "\'", "\\"], "", $authors_result->meta_value) : $authors_result->meta_value;
                $insert_query .= "(" . $authors_result->user_id . ",'" . $first_name . "', ";
                $last_full_name = $first_name . ' ';
                $last_full_name_reversed = ' ' . $first_name;
            } elseif ($authors_result->meta_key == 'last_name') {
                $last_name = strpos($authors_result->meta_value, "'") != false ? str_replace(["'", "\'", "\\"], "", $authors_result->meta_value) : $authors_result->meta_value;
                $last_full_name .= $last_name;
                $last_full_name_reversed = $last_name . $last_full_name_reversed;
                $insert_query .= "'" . $last_name . "', '" . $last_full_name . "', '" . $last_full_name_reversed . "'),";
                $last_full_name = '';
            }
        }
        $last_char = substr($insert_query, -1);
        if ($last_char == ',') {
            $insert_query = substr($insert_query, 0, -1);
        }

        $wpdb->query($insert_query);

        echo "<script>location.replace('index.php');</script>";
    }
}

function foody_add_new_author_to_authors_table()
{
    global $wpdb;
    $author_table_name = $wpdb->prefix . 'foody_authors_names';

    if (is_admin() && is_user_logged_in() && current_user_can('administrator')) {
        $author_id = isset($_POST['user_id']) ? $_POST['user_id'] : false;
        $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';

        $first_name = strpos($first_name, "'") != false ? str_replace(["'", "\'", "\\"], "", $first_name) : $first_name;
        $last_name = strpos($last_name, "'") != false ? str_replace(["'", "\'", "\\"], "", $last_name) : $last_name;

        $role = isset($_POST['role']) ? $_POST['role'] : false;

        if ($author_id && $role == 'author' && (!empty($first_name) || !empty($last_name))) {
            $full_name = $first_name . ' ' . $last_name;
            $reversed_full_name = $last_name . ' ' . $first_name;
            $insert_update_query = "INSERT INTO {$author_table_name} (`author_id`, `first_name`, `last_name`, `full_name`, `reversed_full_name`) VALUES ({$author_id}, '{$first_name}', '{$last_name}', '{$full_name}', ' {$reversed_full_name}') 
ON DUPLICATE KEY UPDATE 
first_name='{$first_name}',
last_name='{$last_name}',
full_name='{$full_name}',
reversed_full_name='{$reversed_full_name}'";

            $wpdb->query($insert_update_query);
        }
    }
}

add_action('edit_user_profile_update', 'foody_add_new_author_to_authors_table');

add_filter('quadmenu_nav_menu_css_class', 'foody_safari_hook_nav_menu_css_class', 10, 3);

function foody_safari_hook_nav_menu_css_class($classes = array(), $item, $args)
{
    if ((strpos($item->post_title, '(') || strpos($item->post_title, ')')) && !preg_match("/[a-zA-Z]/i", $item->post_title)) {
        $classes[] = 'iso-navmenu-item';
    } else {
        $classes[] = '';
    }
    return $classes;
}

add_action('init', 'foody_rem_editor_from_post_type_foody_organizations');
function foody_rem_editor_from_post_type_foody_organizations()
{
    remove_post_type_support('foody_organizations', 'editor');
}

function foody_remove_meta_boxes_post_type_foody_organizations()
{
    remove_meta_box('wpseo_meta', 'foody_organizations', 'normal');
    remove_meta_box('postexcerpt', 'foody_organizations', 'normal');
    remove_meta_box('trackbacksdiv', 'foody_organizations', 'normal');
    remove_meta_box('commentstatusdiv', 'foody_organizations', 'normal');
    remove_meta_box('authordiv', 'foody_organizations', 'normal');
    remove_meta_box('postimagediv', 'foody_organizations', 'normal');


}

add_action('add_meta_boxes', 'foody_remove_meta_boxes_post_type_foody_organizations', 100);

function foody_setInterval($func, $milliseconds)
{
    $continue_interval = true;
    $seconds = (int)$milliseconds / 1000;
    while ($continue_interval) {
        $continue_interval = $func();
        if ($continue_interval) {
            sleep($seconds);
        }
    }
}

function bit_recurring_fetch_transaction_status()
{
    if (defined('FOODY_BIT_FETCH_STATUS_PROCESS')) {
        // Make sure this event hasn't been scheduled
        if (!wp_next_scheduled('foody_bit_fetch_status_processes')) {
            // Schedule the event
            wp_schedule_event(time(), 'one_minute', 'foody_bit_fetch_status_processes');
        }
    }
}

add_action('init', 'bit_recurring_fetch_transaction_status');

function redirect_social_login($user_id, $provider, $hybridauth_user_profile, $redirect_to){
    $redirect_to = get_permalink(get_page_by_path('השלמת-רישום'));
    return $redirect_to;
}
add_action('wsl_hook_process_login_before_wp_safe_redirect', 'redirect_social_login',10,4);

add_filter('body_class', 'foody_body_add_bit_class', 10, 1);
function foody_body_add_bit_class($classes)
{
    $class_to_add = 'foody-payment-bit';

    if (isset($_GET) && (isset($_GET['payment_method']) && $_GET['payment_method'] == __('ביט')) || (isset($_GET['course_id']) && strpos($_GET['course_id'], ',') != false)) {
        $classes[] = $class_to_add;
    }

    return $classes;

}

add_filter('the_content', 'addClassToLinks');
function addClassToLinks($content){
    return str_replace( '<a ', "<a class='post-content-link'", $content);
}