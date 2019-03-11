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
        load_theme_textdomain('foody', get_template_directory() . '/languages');

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

    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, NULL, true );
    wp_enqueue_script( 'jquery' );
//    wp_denqueue_style('foody-style', get_stylesheet_uri());

    wp_enqueue_script('foody-navigation', get_template_directory_uri() . '/resources/js/navigation.js', array(), '20151215', true);

    wp_enqueue_script('foody-skip-link-focus-fix', get_template_directory_uri() . '/resources/js/skip-link-focus-fix.js', array(), '20151215', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply', false, false, true,true);
    }


//    if(is_front_page()){
//        wp_enqueue_script('foody-script', get_template_directory_uri() . '/dist/home.js', false, false, true);
//    }

    if (!is_admin()) {

//        if (is_front_page() || is_home()) {
//            $homepage = foody_get_versioned_asset('homepage');
//            wp_enqueue_script('foody-script', $homepage, false, false, false);
//        }else{
        $style = foody_get_versioned_asset('style');
        wp_enqueue_script('foody-style', $style, false, false, true);

        $asset = foody_get_versioned_asset('main');
        wp_enqueue_script('foody-script', $asset, false, false, true);
//        }
    }

    if (is_page(get_page_by_title('הרשמה'))) {
        wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js');
    }

}

add_action('wp_enqueue_scripts', 'foody_scripts');

add_action('wp_print_styles', 'my_deregister_styles', 100000000000);

function my_deregister_styles()
{
    wp_deregister_style('dashicons');
    wp_dequeue_style('fontawesome');
    wp_deregister_style('fontawesome');
}

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

    return get_template_directory_uri() . "/dist/$name.$assets_version.js";

}

function add_async_attribute($tag, $handle)
{
    if ('foody-script' !== $handle && 'foody-script' !== $handle)
        return $tag;
    return str_replace(' src', ' defer="defer" async="async" src', $tag);
}

add_filter('script_loader_tag', 'add_async_attribute', 10, 2);

function essb_stylebuilder_css_filess(){

}

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );
function wps_deregister_styles() {
    wp_deregister_style( 'contact-form-7' );
}