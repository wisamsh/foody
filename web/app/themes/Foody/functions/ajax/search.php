<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/25/18
 * Time: 4:52 PM
 */

/**
 * Live autocomplete search.
 *
 * @since 1.0.0
 */
function foody_ajax_autocomplete()
{
    $search = stripslashes($_POST['search']);

    $results = new WP_Query(array(
        'post_type' => array('foody_recipe'),
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'orderby' => 'title',
        's' => $search,
    ));


    $items = [];
    if (!empty($results->posts)) {
        foreach ($results->posts as $result) {
            $items[] = [
                'name' => $result->post_title,
                'link' => Foody_Query::get_search_url($result->post_title)
            ];
        }
    }

    $authors = foody_search_user_by_name($search, false);

    if (is_array($authors) && count($authors) > 0) {
        $authors = array_unique($authors);
        foreach ($authors as $author) {
            $user = get_user_by('ID', $author);
            $items[] = [
                'name' => $user->display_name,
                'link' => Foody_Query::get_search_url($user->display_name)
            ];
        }
    }

    wp_send_json_success($items);
}

add_action('wp_ajax_search_site', 'foody_ajax_autocomplete');
add_action('wp_ajax_nopriv_search_site', 'foody_ajax_autocomplete');


/**
 * Live autocomplete search.
 *
 * @since 1.0.0
 */
function foody_ajax_filter()
{
    if (!isset($_POST['data'])) {
        wp_die(foody_ajax_error('no data provided for filter'));
    }

    $filter = $_POST['data'];
    $options = $_POST['options'];

    $foody_search = new Foody_Search();

    $posts = $foody_search->query($filter);

    $posts = array_map('Foody_Post::create', $posts);

    $grid = new FoodyGrid();

    echo $grid->loop($posts, $options['cols'], false);

    die();

}

add_action('wp_ajax_foody_filter', 'foody_ajax_filter');
add_action('wp_ajax_nopriv_foody_filter', 'foody_ajax_filter');


/**
 * TODO move this to relevant functions script
 * @param string $search the search query
 * @param WP_Query $wp_query
 * @return string sql query
 */
function __search_by_title_only($search, $wp_query)
{
    global $wpdb;

    if (empty($search))
        return $search; // skip processing - no search term in query

    $q = $wp_query->query_vars;
    $n = !empty($q['exact']) ? '' : '%';

    $search =
    $searchand = '';

    foreach ((array)$q['search_terms'] as $term) {
        $term = esc_sql($wpdb->esc_like($term));
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $searchand = ' AND ';
    }

    if (!empty($search)) {
        $search = " AND ({$search}) ";
        if (!is_user_logged_in())
            $search .= " AND ($wpdb->posts.post_password = '') ";
    }

    return $search;
}

add_filter('posts_search', '__search_by_title_only', 500, 2);


//function livchem_search_filter($s) {
//    return urldecode($s);
//}
//
//add_filter('get_search_query', 'livchem_search_filter');
//add_filter('the_search_query', 'livchem_search_filter');
//
//function livchem_query_vars_search_filter($query)
//{
//    if ($query->is_search && !is_admin()) {
//        $query->query_vars['s'] = urldecode($query->query_vars['s']);
//    }
//
//    return $query;
//}
//add_action('parse_query', 'livchem_query_vars_search_filter');
