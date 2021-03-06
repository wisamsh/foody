<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/30/18
 * Time: 1:17 PM
 */

// TODO move hooks to the relevant files under Foody/functions/

/**
 * Wrap content with foody class
 *
 * @param $content
 *
 * @return mixed
 */
function foody_content_filter( $content ) {

	$content_class = 'foody-content';

	return '<div class="' . $content_class . '">' . $content . '</div>';

}

add_filter( 'the_content', 'foody_content_filter' );


/**
 * Remove fields from comment form
 *
 * @param $fields
 *
 * @return mixed
 */
function foody_comment_form_fields( $fields ) {
	if ( is_user_logged_in() ) {
		$fields['email']  = '';  //remove default email input
		$fields['author'] = '';//remove default author input
	}
	$fields['url'] = '';  //remove default url input

	return $fields;
}

add_filter( 'comment_form_default_fields', 'foody_comment_form_fields' );


/**
 * Hooks into the page template and
 * overrides the default template.
 * Used to make sure the relevant
 * post types are rendered with Foody's
 * custom templates.
 *
 * @param $template
 *
 * @return string
 */
function default_page_template( $template ) {
	if ( is_singular( array( 'post', 'foody_recipe', 'foody_playlist' ) ) ) {
		$default_template = locate_template( array( 'page-templates/content-with-sidebar.php' ) );
		if ( '' != $default_template ) {
			$template = $default_template;
		}
	}

	return $template;
}

add_filter( 'template_include', 'default_page_template', 10 );

add_filter( 'show_admin_bar', '__return_false' );


/*
 * Custom filter - Foody theme
 * */


/**
 * Hooks into wp_head action hook.
 * prints global js variables.
 * usage: add_filter('foody_js_globals',function($vars))
 * where vars is an array.
 */
function foody_js_globals() {
	global $wp_session;

	if ( ! is_admin() ) {
		// Hookable
		$vars              = apply_filters( 'foody_js_globals', [] );
		$vars['isMobile']  = wp_is_mobile();
		$vars['ajax']      = admin_url( 'admin-ajax.php' );
		$vars['loggedIn']  = is_user_logged_in();
		$vars['imagesUri'] = $GLOBALS['images_dir'];
		$vars['messages']  = foody_js_messages();

		$vars['userRecipesCount'] = 0;
		if ( is_user_logged_in() ) {
			$vars['userRecipesCount'] = empty( $wp_session['favorites'] ) ? 0 : count( $wp_session['favorites'] );
			$vars['loggedInUser']     = wp_get_current_user()->ID;
		}

		$js = wp_json_encode( $vars );

		?>
        <script async defer>
            foodyGlobals = <?php echo $js?>;
        </script>

		<?php
	}
	else{
        $vars['ajax']      = admin_url( 'admin-ajax.php' );
        $js = wp_json_encode( $vars );

        ?>
        <script async defer>
            foodyGlobals = <?php echo $js?>;
        </script>

        <?php
    }
}

function foody_js_messages() {
	$messages = apply_filters( 'foody_js_messages', [ 'general' => [] ] );

	return $messages;
}

add_action( 'wp_head', 'foody_js_globals', - 10000 );
add_action( 'admin_head', 'foody_js_globals', - 10000 );

///**
// * Include posts from authors in the search results where
// * either their display name or user login matches the query string
// *
// * @author danielbachhuber
// */
//add_filter( 'posts_search', 'db_filter_authors_search' );
//function db_filter_authors_search( $posts_search ) {
//
//    // Don't modify the query at all if we're not on the search template
//    // or if the LIKE is empty
//    if ( !is_search() || empty( $posts_search ) )
//        return $posts_search;
//
//    global $wpdb;
//    // Get all of the users of the blog and see if the search query matches either
//    // the display name or the user login
//    add_filter( 'pre_user_query', 'db_filter_user_query' );
//    $search = sanitize_text_field( get_query_var( 's' ) );
//    $args = array(
//        'count_total' => false,
//        'search' => sprintf( '*%s*', $search ),
//        'search_fields' => array(
//            'display_name',
//            'user_login',
//        ),
//        'fields' => 'ID',
//    );
//    $matching_users = get_users( $args );
//    remove_filter( 'pre_user_query', 'db_filter_user_query' );
//    // Don't modify the query if there aren't any matching users
//    if ( empty( $matching_users ) )
//        return $posts_search;
//    // Take a slightly different approach than core where we want all of the posts from these authors
//    $posts_search = str_replace( ')))', ")) OR ( {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $matching_users ) ) . ")))", $posts_search );
//    return $posts_search;
//}
///**
// * Modify get_users() to search display_name instead of user_nicename
// */
//function db_filter_user_query( &$user_query ) {
//
//    if ( is_object( $user_query ) )
//        $user_query->query_where = str_replace( "user_nicename LIKE", "display_name LIKE", $user_query->query_where );
//    return $user_query;
//}


/**
 * @param $tag
 * @param bool $priority
 *
 * @return bool
 */
function foody_remove_all_filters( $tag, $priority = false ) {
	global $wp_filter;

	if ( isset( $wp_filter[ $tag ] ) ) {
		$wp_filter[ $tag ]->remove_all_filters( $priority );
		if ( ! $wp_filter[ $tag ]->has_filters() ) {
			unset( $wp_filter[ $tag ] );
		}
	}

	return true;
}


function foody_add_filters_by_condition( $filters, $callback, $callback_args = [] ) {

	if ( is_callable( $callback ) ) {
		$pass = call_user_func_array( $callback, $callback_args );
		if ( $pass ) {
			foreach ( $filters as $filter ) {

				if ( ! isset( $filter['priority'] ) ) {
					$filter['priority'] = 10;
				}
				if ( ! isset( $filter['accepted_args'] ) ) {
					$filter['accepted_args'] = 1;
				}

				add_filter( $filter['tag'], $filter['callback'], $filter['priority'], $filter['accepted_args'] );
			}
		}
	}
}


function foody_custom_logo_link($is_print = false) {

	$custom_logo_id  = get_theme_mod( 'custom_logo' );
	$site_url        = get_home_url();
	$custom_site_url = get_theme_mod( 'foody_logo_link' );
	if ( isset( $custom_site_url ) && ! empty( $custom_site_url ) ) {
		$site_url = $custom_site_url;
	}

	$html = "";
	if ( $custom_logo_id ) {
		$custom_logo_attr = array(
			'class'    => 'custom-logo',
			'itemprop' => 'logo',
		);

		$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
		if ( empty( $image_alt ) ) {
			$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
		}

		$format = $is_print ? '<a href="%1$s" class="custom-logo-link print-header-image" rel="home" itemprop="url">%2$s</a>' : '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>';
		$html = sprintf( $format,
			esc_url( $site_url ),
			wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr )
		);

	}

	// Return
	return $html;
}

add_filter( 'get_custom_logo', 'foody_custom_logo_link' );