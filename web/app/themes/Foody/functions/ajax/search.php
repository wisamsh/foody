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
        'posts_per_page' => 15,
        'orderby' => 'title',
        's' => $search,
    ));


    $items = array();
    if (!empty($results->posts)) {
        foreach ($results->posts as $result) {
            $items[] = [
                'name' => $result->post_title,
                'link' => get_search_link($result->post_title)
            ];
        }
    }

    $terms = get_terms('category', array(
        'name__like' => $search,
        'number' => 5,
        'hide_empty' => false // Optional
    ));

    if (!is_wp_error($terms)) {
        /** @var WP_Term $term */
        foreach ($terms as $term) {
            $items[] = [
                'name' => $term->name,
                'link' => get_search_link($term->name)
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

    $posts = array_map('Foody_Post::create',$posts);

    $grid = new RecipesGrid();

    // TODO pass cols
    echo $grid->loop($posts,$options['cols'],false);

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

