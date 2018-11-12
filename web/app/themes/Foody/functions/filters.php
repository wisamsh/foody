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
 * @param $content
 * @return mixed
 */
function foody_content_filter($content)
{

    $content_class = 'foody-content';

    return '<div class="' . $content_class . '">' . $content . '</div>';

}

add_filter('the_content', 'foody_content_filter');


/**
 * Remove fields from comment form
 * @param $fields
 * @return mixed
 */
function foody_comment_form_fields($fields)
{
    $fields['email'] = '';  //remove default email input
    $fields['url'] = '';  //remove default url input
    $fields['author'] = '';//remove default author input
    return $fields;
}

add_filter('comment_form_default_fields', 'foody_comment_form_fields');


/**
 * Hooks into the page template and
 * overrides the default template.
 * Used to make sure the relevant
 * post types are rendered with Foody's
 * custom templates.
 * @param $template
 * @return string
 */
function default_page_template($template)
{
    if (is_singular(array('post', 'foody_recipe', 'foody_playlist'))) {
        $default_template = locate_template(array('page-templates/content-with-sidebar.php'));
        if ('' != $default_template) {
            $template = $default_template;
        }
    }

    return $template;
}

add_filter('template_include', 'default_page_template', 10);

add_filter('show_admin_bar', '__return_false');


/*
 * Custom filter - Foody theme
 * */


/**
 * Hooks into wp_head action hook.
 * prints global js variables.
 * usage: add_filter('foody_js_globals',function($vars))
 * where vars is an array.
 */
function foody_js_globals()
{
    if (!is_admin()) {
        // Hookable
        $vars = apply_filters('foody_js_globals', []);
        $vars['isMobile'] = wp_is_mobile();
        $vars['ajax'] = admin_url('admin-ajax.php');
        $vars['loggedIn'] = is_user_logged_in() ? 'true' : 'false';
        $vars['imagesUri'] = $GLOBALS['images_dir'];

        $js = wp_json_encode($vars);

        ?>
        <script>
            foodyGlobals = <?php echo $js?>;
        </script>

        <?php
    }
}

add_action('wp_head', 'foody_js_globals', -10000);

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

