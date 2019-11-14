<?php
/**
 * Foody functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Foody
 */


if ( ! function_exists( 'foody_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function foody_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Foody, use a find and replace
		 * to change 'foody' to the name of your theme in all the template files.
		 */
//        load_theme_textdomain('foody', get_template_directory() . '/languages');

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1'        => esc_html__( 'Primary', 'foody' ),
			'footer-pages'  => esc_html__( 'Footer Pages', 'foody' ),
			'footer-links'  => esc_html__( 'Footer Links', 'foody' ),
			'channels-menu' => esc_html__( 'Channels Menu', 'foody' )
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'foody_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'foody_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function foody_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'foody_content_width', 640 );
}

add_action( 'after_setup_theme', 'foody_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function foody_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'foody' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'foody' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}

add_action( 'widgets_init', 'foody_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function foody_scripts() {

	wp_deregister_script( 'jquery' );
	wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js', array(), null, false );

	wp_enqueue_script( 'foody-navigation', get_template_directory_uri() . '/resources/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'foody-skip-link-focus-fix', get_template_directory_uri() . '/resources/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply', false, false, true, true );
	}

	if ( ! is_admin() ) {

		$style = foody_get_versioned_asset( 'style' );
		wp_enqueue_script( 'foody-style', $style, false, false, true );

		$post_content = '';
		$current_post = get_post();

		if ( ! empty( $current_post ) ) {
			$post_content = $current_post->post_content;
		}

		$lazy_asset = foody_get_versioned_asset( 'lazy' );
		wp_enqueue_script( 'foody-script-lazy', $lazy_asset, false, false, false );


		// Homepage
		if ( is_front_page() || is_home() || is_404() ) {
			$homepage_asset = foody_get_versioned_asset( 'homepage' );
			wp_enqueue_script( 'foody-script-home', $homepage_asset, false, false, true );
		}

		if ( is_page_template( 'page-templates/profile.php' ) ) {
			$profile_asset = foody_get_versioned_asset( 'profile' );
			wp_enqueue_script( 'foody-script-profile', $profile_asset, false, false, true );
		}

		if ( has_shortcode( $post_content, 'foody-approvals' ) || is_page_template( 'page-templates/foody-campaign.php' ) || is_page_template( 'page-templates/foody-campaign-extended.php' ) ) {
			$campaign_asset = foody_get_versioned_asset( 'campaign' );
			wp_enqueue_script( 'foody-script-campaign', $campaign_asset, false, false, true );
		}

		if ( is_category() ) {
			$categories_asset = foody_get_versioned_asset( 'categories' );
			wp_enqueue_script( 'foody-script-categories', $categories_asset, false, false, true );
		}

		if ( is_page_template( 'page-templates/channel.php' ) ) {
			$channel_asset = foody_get_versioned_asset( 'channel' );
			wp_enqueue_script( 'foody-script-channel', $channel_asset, false, false, true );
		}

		if (
			is_search() ||
			( is_page_template( 'page-templates/centered-content.php' ) && ! has_shortcode( $post_content, 'foody_team' ) ) ||
			is_page_template( 'page-templates/categories.php' ) ||
			has_shortcode( $post_content, 'contact-form-7' )
		) {
			$general_asset = foody_get_versioned_asset( 'general' );
			wp_enqueue_script( 'foody-script-general', $general_asset, false, false, true );
		}

		if ( is_page_template( 'page-templates/content-with-sidebar.php' ) && is_single()
		     && ! in_array( get_post_type(), [ 'foody_ingredient', 'foody_accessory', 'foody_technique' ] ) ) {
			$post_asset = foody_get_versioned_asset( 'post' );
			wp_enqueue_script( 'foody-script-recipe', $post_asset, false, false, true );
		}

		if ( is_page_template( 'page-templates/foody-course.php' ) ) {
			$course_asset = foody_get_versioned_asset( 'course' );
			wp_enqueue_script( 'foody-script-course', $course_asset, false, false, true );
		}

        if ( is_page_template( 'page-templates/foody-course-efrat.php' ) ) {
            $course_asset = foody_get_versioned_asset( 'course' );
            wp_enqueue_script( 'foody-script-course', $course_asset, false, false, true );
        }

		if ( is_page_template( 'page-templates/items.php' ) ) {
			$items_asset = foody_get_versioned_asset( 'items' );
			wp_enqueue_script( 'foody-script-recipe', $items_asset, false, false, true );
		}

		if ( has_shortcode( $post_content, 'foody-login' ) ) {
			$login_asset = foody_get_versioned_asset( 'login' );
			wp_enqueue_script( 'foody-script-login', $login_asset, false, false, true );
		}

		if ( has_shortcode( $post_content, 'foody-register' ) ) {
			$register_asset = foody_get_versioned_asset( 'register' );
			wp_enqueue_script( 'foody-script-register', $register_asset, false, false, true );
			wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', false, false, true );
		}

		if ( is_author() ) {
			$author_asset = foody_get_versioned_asset( 'author' );
			wp_enqueue_script( 'foody-script-author', $author_asset, false, false, true );
		}

		if ( is_tag() || in_array( get_post_type(), [ 'foody_ingredient', 'foody_accessory', 'foody_technique' ] ) ) {
			$tag_asset = foody_get_versioned_asset( 'tag' );
			wp_enqueue_script( 'foody-script-tag', $tag_asset, false, false, true );
		}

		if ( has_shortcode( $post_content, 'foody_team' ) ) {
			$team_asset = foody_get_versioned_asset( 'team' );
			wp_enqueue_script( 'foody-script-team', $team_asset, false, false, true );
		}

		if ( get_post_type() == 'foody_playlist' ) {
			$playlist_asset = foody_get_versioned_asset( 'playlist' );
			wp_enqueue_script( 'foody-script-plalist', $playlist_asset, false, false, true );
		}
	}


}

add_action( 'wp_enqueue_scripts', 'foody_scripts' );

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

function foody_add_footer_styles() {

	wp_register_style( 'sb_instagram_styles', content_url( '/plugins/instagram-feed/css/sb-instagram.min.css' ), array(), SBIVER );
	wp_enqueue_style( 'sb_instagram_styles' );

	if ( is_single() ) {
		wp_register_style( 'wp-postratings', content_url( '/plugins/wp-postratings/css/postratings-css.css' ), false, WP_POSTRATINGS_VERSION, 'all' );
		wp_enqueue_style( 'wp-postratings' );

		wp_register_style( 'wp-postratings-rtl', content_url( '/plugins/wp-postratings/css/postratings-css-rtl.css' ), false, WP_POSTRATINGS_VERSION, 'all' );
		wp_enqueue_style( 'wp-postratings-rtl' );
	}
}

add_action( 'get_footer', 'foody_add_footer_styles', 10000000000000000 );

add_action( 'wp_print_styles', 'my_deregister_styles', 100000000000 );

function my_deregister_styles() {
	wp_deregister_style( 'dashicons' );
	wp_dequeue_style( 'fontawesome' );
	wp_deregister_style( 'fontawesome' );
}

function foody_custom_dequeue() {

	wp_deregister_style( 'sb_instagram_styles' );
	wp_dequeue_style( 'sb_instagram_styles' );

	wp_deregister_style( 'wp-postratings' );
	wp_dequeue_style( 'wp-postratings' );
	wp_deregister_style( 'wp-postratings-rtl' );
	wp_dequeue_style( 'wp-postratings-rtl' );

	global $wp_styles;

	foreach ( $wp_styles->registered as $handle => $args ) {
		if ( $handle != 'easy-social-share-buttons' ) {
			if ( preg_match( '/^essb-/', $handle ) ) {
				wp_deregister_style( $handle );
				wp_dequeue_style( $handle );
			}
		}
	}

}

add_action( 'wp_print_styles', 'foody_custom_dequeue', 9999 );
add_action( 'wp_enqueue_scripts', 'foody_custom_dequeue', 9999 );
add_action( 'wp_head', 'foody_custom_dequeue', 9999 );

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


register_nav_menus( array(
	'primary' => __( 'Primary Menu', 'foody' ),
) );

require_once get_template_directory() . '/functions/includes.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


function admin_theme_style() {
	$asset = foody_get_versioned_asset( 'admin' );
	wp_enqueue_script( 'admin-script', $asset, false, false, true );
}


add_action( 'admin_enqueue_scripts', 'admin_theme_style' );
add_action( 'login_enqueue_scripts', 'admin_theme_style' );


function foody_get_versioned_asset( $name ) {
	$assets_version = file_get_contents( get_template_directory() . '/build/version-hash.txt' );

	return get_template_directory_uri() . "/dist/$name.$assets_version.js#asyncload";

}

function add_async_attribute( $tag, $handle ) {
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
		'foody-script-plalist',
		'foody-white-label',
		'sb_instagram_scripts',
		'ui-a11y.js',
		'wsl-widget'
	);
	foreach ( $scripts_to_defer as $defer_script ) {
		if ( $defer_script === $handle ) {
			return str_replace( ' src', ' async defer src', $tag );
		}
	}

	return $tag;
}

//add_filter( 'script_loader_tag', 'add_async_attribute', 10, 2 );

function essb_stylebuilder_css_filess() {

}

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );
function wps_deregister_styles() {
	wp_deregister_style( 'contact-form-7' );
}

// The callback function for the action hook bellow
function any_script_in_footer() {
	if ( ! is_admin() ) {
		// Call the list with all the registered scripts
		global $wp_scripts;

		if ( isset ( $wp_scripts->registered ) && ! empty ( $wp_scripts->registered ) && is_array( $wp_scripts->registered ) ) {
			foreach ( $wp_scripts->registered as $idx => $script ) {
				if ( isset( $wp_scripts->registered[ $idx ]->extra ) && is_array( $wp_scripts->registered[ $idx ]->extra ) ) {

					// Set any of the scripts to belong in the footer group
					$wp_scripts->registered[ $idx ]->extra['group'] = 1;
				}
			}
		}
	}
}

// Call the callback function with the `wp_print_scripts` hook at the very end
// of the callbacks cue
//add_action('wp_print_scripts', 'any_script_in_footer', 10000000000000);


function hide_w3tc() {
	if ( defined( 'WP_ENV' ) && WP_ENV == 'production' ) {
		remove_menu_page( 'w3tc_dashboard' );
	}
}

add_action( 'network_admin_menu', 'hide_w3tc', 111 );
add_action( 'admin_menu', 'hide_w3tc', 111 );


add_action( 'admin_bar_menu', 'foody_remove_from_admin_bar', 999 );
/**
 * @param $wp_admin_bar WP_Admin_Bar
 */
function foody_remove_from_admin_bar( $wp_admin_bar ) {
	if ( defined( 'WP_ENV' ) && WP_ENV == 'production' ) {
		if ( ! current_user_can( 'manage_network' ) ) {
			$wp_admin_bar->remove_node( 'w3tc' );
		} else {
			$wp_admin_bar->remove_node( 'w3tc_settings_general' );
			$wp_admin_bar->remove_node( 'w3tc_settings_extensions' );
			$wp_admin_bar->remove_node( 'w3tc_settings_general' );
		}
	}

}

add_filter( 'manage_foody_ingredient_posts_columns', 'set_custom_edit_foody_ingredient_columns' );
function set_custom_edit_foody_ingredient_columns($columns) {
    $columns['amount'] = __( 'כמות', 'your_text_domain' );

    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_foody_ingredient_posts_custom_column' , 'custom_foody_ingredient_column', 10, 2 );
function custom_foody_ingredient_column( $column, $post_id ) {
    global $wpdb;
    $ingredient = get_the_title($post_id);
    switch ( $column ) {

        case 'amount' :
            $query = "SELECT count(meta_value) as count 
            FROM {$wpdb->postmeta} where meta_key like  'ingredients_ingredients_groups_%_ingredients_%_ingredient'  
            AND meta_value = (SELECT ID FROM {$wpdb->posts} where post_title = '$ingredient' and post_status = 'publish' AND post_type = 'foody_ingredient')
            group by meta_value";

            $results = $wpdb->get_results($query);
//            if (is_array($toPush) && !empty($toPush)) {
//                $results[$ingredient] = $toPush[0]->count;
//            }
        if(empty($results)){
            $amount = 0;
        }
        else {
            $amount = $results[0]->count;
        }
            echo $amount;
            break;

    }
}
