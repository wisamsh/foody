<?php
/**
 * This functions file include head
 * modifications and actions (most probably usage of the wp_head action hook).
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 11:15 PM
 */

add_action( 'wp_head', 'Foody_Header::facebook_init' );


function get_page_type() {

	$type = 'article';

	if ( is_home() || is_front_page() ) {
		$type = 'home';
	} elseif ( is_category() ) {
		$type = 'category';
	} elseif ( is_search() ) {
		$type = 'search';
	} elseif ( is_author() ) {
		$type = 'author';
	} elseif ( is_single() ) {
		$post_type = get_post_type();

		if ( ! empty( $post_type ) && $post_type != 'post' ) {
			$type = str_replace( 'foody_', '', $post_type );
		}
	} elseif ( is_page_template( 'page-templates/categories.php' ) ) {

		$type = 'categories';

	} elseif ( is_page_template( 'page-templates/profile.php' ) ) {

		$type = 'profile';

	} elseif ( is_page( 'הנבחרת שלנו' ) ) {

		$type = 'team';

    } elseif ( is_page_template( 'page-templates/foody-campaign.php' ) || is_page_template( 'page-templates/foody-campaign-extended.php' ) ) {
	    $type = 'campaign';
    }

	return $type;
}


function foody_js_globals_main( $vars ) {

	$vars['queryPage']     = apply_filters( 'foody_page_query_var', Foody_Query::$page );
	$vars['objectID']      = get_queried_object_id();
	$vars['title']         = get_the_title();
	$vars['type']          = get_page_type();
	$vars['postsPerPage']  = get_option( 'posts_per_page' );
	$vars['mixpanelToken'] = MIXPANEL_TOKEN;


	if ( is_single() ) {

		$vars['post'] = [
			'ID'    => get_the_ID(),
			'type'  => get_post_type(),
			'title' => get_the_title()
		];
	}

	$queried_object = get_queried_object();
	if ( is_category() || is_tag() ) {
		$vars['title'] = $queried_object->name;
	} elseif ( is_author() ) {
		$vars['title'] = $queried_object->data->display_name;
	}


	return $vars;
}

add_filter( 'foody_js_globals', 'foody_js_globals_main' );


function is_tablet( $vars ) {
	$tablet_browser = foody_is_tablet();

	$vars['isTablet'] = $tablet_browser;

	return $vars;
}

add_filter( 'foody_js_globals', 'is_tablet' );


function campaign_name( $vars ) {
	if ( get_page_type() == 'campaign' ) {
		if ( is_user_logged_in() ) {
			$vars['extended_campaign_url']   = get_field( 'extended_campaign_url' );
			$vars['seen_extended_approvals'] = Foody_User::user_has_meta( 'seen_extended_approvals' );
		}
	}
	$registration_page = get_page_by_title( 'הרשמה' );
    $vars['campaign_name'] = get_field( 'campaign_name', $registration_page );
	$vars['campaign_url'] = get_field( 'campaign_link', $registration_page );

	return $vars;
}

add_filter( 'foody_js_globals', 'campaign_name' );

function foody_set_og_image()
{
    if (is_author()) {

		$author = new Foody_Author();

		$author_image = $author->topic_image();
		$image        = "<meta property=\"og:image\" content=\"$author_image\">";

		$image .= '<meta property="og:image:width" content="96">';
		$image .= '<meta property="og:image:height" content="96">';
		echo $image;
	}


}

add_action( 'wp_head', 'foody_set_og_image' );


function foody_hide_mobile_filter( $vars ) {
	$queried_object = get_queried_object();
	$show_filters   = get_field( 'show_filters', $queried_object );
	if ( $show_filters === false ) {
		$vars['hideFilter'] = true;
	}

	return $vars;
}

add_filter( 'foody_js_globals', 'foody_hide_mobile_filter' );


function foody_env_scripts() {
	$scripts = [
		'http://foody.moveodevelop.com' => [
			"    (function(h,o,t,j,a,r){

        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};

        h._hjSettings={hjid:1114919,hjsv:6};

        a=o.getElementsByTagName('head')[0];

        r=o.createElement('script');r.async=1;

        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;

        a.appendChild(r);

    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');"
		],
		'https://foody.co.il'           => []
	];

	if ( isset( $scripts[ home_url() ] ) ) {
		$env_scripts = $scripts[ home_url() ];

		foreach ( $env_scripts as $script ) {

			?>
            <script async defer>

				<?php echo $script ?>

            </script>
			<?php
		}
	}
}

add_action( 'wp_head', 'foody_env_scripts' );

function foody_page_content_pagination() {
	if ( is_category() || is_home() || is_front_page() ) {
		$page = get_query_var( 'page' );
		if ( isset( $_GET['page'] ) ) {
			$page = $_GET['page'];
			if ( ! is_numeric( $page ) ) {
				$page = 1;
			}
		}
		if ( empty( $page ) ) {
			$page = 1;
		}
		$args = [
			'post_type'   => [ 'foody_recipe', 'foody_playlist', 'post' ],
			'post_status' => 'publish',
			'fields'      => 'ids'
		];

		$posts_per_page = get_option( 'posts_per_page' );
		$link           = home_url();

		if ( is_category() ) {
			$args['cat'] = get_queried_object_id();
			$link        = get_term_link( get_queried_object_id() );
		}

		$q = new WP_Query( $args );

		$posts_count = $q->found_posts;
		if ( is_numeric( $posts_count ) ) {
			$posts_count = intval( $posts_count );
		} else {
			$posts_count = 0;
		}

		$max_pages = $posts_count / $posts_per_page;

		$prev      = $page - 1;
		$next      = $page + 1;
		$q_or_path = '/page/';
		if ( is_category() ) {
			$q_or_path = '?page=';
		}
		if ( $prev > 0 ) {
			$href = $link . $q_or_path . $prev;
			echo '<link id="pagination-prev" rel="prev" href="' . $href . '">';
		}

		if ( $next <= $max_pages ) {
			$href = $link . $q_or_path . $next;
			echo '<link id="pagination-next" rel="next" href="' . $href . '">';
		}
	}
}

add_action( 'wp_head', 'foody_page_content_pagination' );


function add_filter_query_arg( $vars ) {
	$vars['filterQueryArg'] = Foody_Query::$filter_query_arg;

	return $vars;
}

add_filter( 'foody_js_globals', 'add_user_data_globals' );

function add_user_data_globals( $vars ) {
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		$social  = get_user_meta( $user_id, 'wsl_current_provider', true );
		if ( ! empty( $social ) ) {
			$vars['user'] = [
				'social_type' => $social
			];
		}
	}

	return $vars;
}

add_filter( 'foody_js_globals', 'add_filter_query_arg' );

function foody_style_placeholder() {
	?>
    <style>
        body {
            -webkit-transition: opacity .15s;
            -moz-transition: opacity .15s;
            -ms-transition: opacity .15s;
            -o-transition: opacity .15s;
            transition: opacity .15s;
            opacity: 0;
        }
    </style>
	<?php
}


add_action( 'wp_head', 'foody_style_placeholder' );


function add_bg_class( $classes ) {

	$bg_image = foody_get_background_image();

	$has_background = ! empty( $bg_image );

	$bg_class = $has_background ? 'has-background' : '';

	$classes[] = $bg_class;

	return $classes;
}

add_filter( 'body_class', 'add_bg_class' );

function foody_customize_colors_css() {
	$titles_color_css      = get_theme_mod( 'foody_title_color' );
	$subtitles_color_css   = get_theme_mod( 'foody_subtitle_color' );
	$text_color_css        = get_theme_mod( 'foody_text_color' );
	$links_color_css       = get_theme_mod( 'foody_links_color' );
	$links_hover_color_css = get_theme_mod( 'foody_links_hover_color' );
	$show_titles_underline = get_theme_mod( 'foody_show_titles_underline' );
	$foody_underline_color = get_theme_mod( 'foody_underline_color' );

	if ( ! empty( $show_titles_underline ) && ! empty( $foody_underline_color ) ) {
		?>
        <style type="text/css">
            .title {
                text-decoration: underline;
                text-decoration-color: <?php echo $foody_underline_color ?> !important;
            }

            .title > a {
                text-decoration: underline;
                text-decoration-color: <?php echo $foody_underline_color ?> !important;
            }
        </style>
		<?php

	}
	if ( ! empty( $titles_color_css ) ) {
		?>
        <style type="text/css">
            :root {
                --color__text-title: <?php echo $titles_color_css; ?>;
                --color__primary: <?php echo $titles_color_css; ?>;
            }
        </style>
		<?php
	}

	if ( ! empty( $links_color_css ) ) {
		?>
        <style type="text/css">
            :root {
                --color__link: <?php echo $links_color_css; ?>;
            }
        </style>
		<?php
	}

	if ( ! empty( $text_color_css ) ) {
		?>
        <style type="text/css">
            :root {
                --color__text-main: <?php echo $text_color_css; ?>;
            }
        </style>
		<?php
	}

	if ( ! empty( $links_hover_color_css ) ) {
		?>
        <style type="text/css">
            :root {
                --color__link-hover: <?php echo $links_hover_color_css; ?>;
            }
        </style>
		<?php
	}
}

add_action( 'wp_head', 'foody_customize_colors_css' );