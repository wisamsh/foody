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
        'post_type' => array('foody_recipe', 'post'),
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'orderby' => 'title',
        's' => $search,
    ));

    $types = ['foody_ingredient', 'foody_feed_channel'];
    $items = [];

    if (!empty($results->posts)) {
        foreach ($results->posts as $result) {
            $posts[] = [
                'name' => $result->post_title,
                'link' => get_permalink($result->ID),
                'type' => 'post'
                //'link' => str_replace('"','', Foody_Query::get_search_url($result->post_title))
            ];
        }
        $items = array_merge($posts, $items);
    }

    foreach ($types as $type) {
        if ($type == 'foody_feed_channel') {
            $feed_areas_for_auto = check_feed_areas_suggetions($search);
            if (!empty($feed_areas_for_auto)) {
                $items = array_merge($feed_areas_for_auto, $items);
            }
        } else {
            $results = find_posts_by_title_and_type($search, $type, true);
            if (is_array($results) && count($results) > 0) {
                $results = array_map(function ($result) {
                    return [
                        'name' => $result->post_title,
                        'link' => get_permalink($result->ID),
                        'type' => 'ingredient'
                    ];
                }, $results);
            }
            $items = array_merge($results, $items);
        }
    }

    $authors = foody_search_user_by_name($search, false);

    if (is_array($authors) && count($authors) > 0) {
        $authors = array_unique($authors);
        $authors = array_map(function ($author) {
            $user = get_user_by('ID', $author);

            return [
                'name' => $user->display_name,
                'link' => Foody_Query::get_search_url($user->display_name),
                'type' => 'author'
            ];
        }, $authors);

        $items = array_merge($authors, $items);

    }

    wp_send_json_success($items);
}

add_action('wp_ajax_search_site', 'foody_ajax_autocomplete');
add_action('wp_ajax_nopriv_search_site', 'foody_ajax_autocomplete');


/**
 * Sidebar filter
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

    $context_args = [];
    if (isset($_POST['data']['context_args'])) {
        $context_args = $_POST['data']['context_args'];
        if (!is_array($context_args)) {
            $context_args = [$context_args];
        }
    }

    $context = $_POST['data']['context'];

    // Creating WP_args for search query parameter.
    $wp_args = [];
    $foody_query = Foody_Query::get_instance();
    $query_args = $foody_query->get_query($context, $context_args);
    $wp_args = array_merge($wp_args, $query_args);


    $foody_search = new Foody_Search($context, $context_args);


    if (is_array($_POST) &&
        isset($_POST['data']) &&
        ((isset($_POST['action']) && $_POST['action'] = "foody_filter" && $_POST['action'] = "foody_filter") ||
            (is_array($_REQUEST) && isset($_REQUEST['action']) && $_REQUEST['action'] = "foody_filter")) &&
        isset($_POST['data']['context']) &&
        $_POST['data']['context'] === 'category' &&
        isset($_POST['data']['context_args']) &&
        is_array($_POST['data']['context_args']) &&
        $_POST['data']['context_args'][0] &&
        !isset($_POST['data']['types'])) {

        $pinned_posts = get_field('pinned_recipes', get_term_by('term_taxonomy_id', $_POST['data']['context_args'][0]));
        if ($pinned_posts) {
            //'post__not_in' => $recipes_ids,
            $recipes_ids = array_map(function ($item) {
//                    /** @var Foody_Recipe $recipe */
                $item['recipe']->pinned = $_POST['data']['context_args'][0];
                return $item['recipe']->ID;
            }, $pinned_posts);

            $wp_args['post__not_in'] = $recipes_ids;
        }
    }

    $query = $foody_search->query($filter, $wp_args);

    $posts = $query['posts'];

    if (!empty($pinned_posts)) {
        $pinned_posts = array_reverse($pinned_posts);
        foreach ($pinned_posts as $post) {
            array_unshift($posts, $post['recipe']);
        }
    }

    $posts = array_map('Foody_Post::create', $posts);

    $grid = new FoodyGrid();

    $res = [
        'next' => count($posts) < $query['found'],
        'count' => count($posts),
        'found' => $query['found']
    ];


    if (!empty($posts) && !empty(array_filter($posts, array($grid, 'is_post_displayable')))) {
        $res['items'] = $grid->loop($posts, $options['cols'], false);
    } else {
        $res['items'] = foody_get_template_part(get_template_directory() . '/template-parts/no-results.php', ['return' => true]);
        $res['next'] = false;
    }


    wp_send_json_success($res);

}

add_action('wp_ajax_foody_filter', 'foody_ajax_filter');
add_action('wp_ajax_nopriv_foody_filter', 'foody_ajax_filter');

// Feed filter Ajax
function foody_feed_ajax_filter()
{
    if (!isset($_POST['data'])) {
        wp_die(foody_ajax_error('no data provided for filter'));
    }

    $filter = $_POST['data'];
    $options = $_POST['options'];

    $context_args = [];
    if (isset($_POST['data']['context_args'])) {
        $context_args = $_POST['data']['context_args'];
        if (!is_array($context_args)) {
            $context_args = [$context_args];
        }
    }

    $context = $_POST['data']['context'];

    // Creating WP_args for search query parameter.
    // The ID comes from -> $context_arg[0]
    if( isset($context_args[0]) ){
        $wp_args = [];
        $all_recipes = get_field('blocks',$context_args[0])[0]['items'];
        if ( !empty( $all_recipes ) ) {
            $foody_query = Foody_Query::get_instance();
            $query_args = $foody_query->get_query($context, $context_args);
            $wp_args = array_merge($wp_args, $query_args);

            $foody_search = new Foody_Search($context, $context_args);

            if($wp_args && $wp_args['post__in']){
                $wp_args['post__in'] = array_map(function ($recipe){
                    return $recipe['post']->ID;
                }, $all_recipes);
            }

            $query = $foody_search->query($filter, $wp_args);

            $posts = $query['posts'];

            $posts = array_map('Foody_Post::create', $posts);

            $grid = new FoodyGrid();

            $res = [
                'next' => count($posts) < $query['found'],
                'count' => count($posts),
                'found' => $query['found']
            ];


            if (!empty($posts) && !empty(array_filter($posts, array($grid, 'is_post_displayable')))) {
                $res['items'] = $grid->loop($posts, $options['cols'], false);
            } else {
                $res['items'] = foody_get_template_part(get_template_directory() . '/template-parts/no-results.php', ['return' => true]);
                $res['next'] = false;
            }


            wp_send_json_success($res);
        }
    }




}

add_action('wp_ajax_foody_feed_filter', 'foody_feed_ajax_filter');
add_action('wp_ajax_nopriv_foody_feed_filter', 'foody_feed_ajax_filter');


/**
 * TODO move this to relevant functions script
 *
 * @param string $search the search query
 * @param WP_Query $wp_query
 *
 * @return string sql query
 */
function __search_by_title_only($search, $wp_query)
{
    global $wpdb;
    $char_limit = 50;

    if (empty($search)) {
        return $search;
    } // skip processing - no search term in query
    $is_user = false;
    $is_ingredient = false;
    $q = $wp_query->query_vars;
    // max chars => 50
    if (isset($q['s']) && mb_strlen($q['s']) > $char_limit) {
        $temp = $q['s'];
        $q['s'] = mb_substr($q['s'], 0, $char_limit);
        //str_replace($temp, $q['s'], $search);
    }
    $n = !empty($q['exact']) ? '' : '%';

    if ((is_search() || (!empty($_POST) && ((isset($_POST['action']) && $_POST['action'] == 'load_more') || (isset($_POST['action']) && $_POST['action'] == 'foody_filter'))))
        && isset($q['s'])) {
        if (!get_page_by_title($q['s'], OBJECT, 'post') && !get_page_by_title($q['s'], OBJECT, 'foody_recipe')) {
            $users = foody_search_user_by_name($q['s']);
            if (!isset($users) || empty($users)) {
                $is_user = false;
                $ingredient = find_posts_by_title_and_type($q['s'], 'foody_ingredient', false, true);
                if (!empty($ingredient)) {
                    $is_ingredient = true;
                }
            } else {
                $is_user = true;
            }
        }
    }

    $search =
    $searchand = '';

    $counter = 0;
    $index = 0;
    $users_amount = count((array)$q['search_terms']);
    $apostrophes_types = ["’", "׳", "'"];
    $has_apostrophes = false;
    $two_options_for_title = false;


    foreach ((array)$q['search_terms'] as $term) {
        if (strpos($q['s'], "’") || strpos($q['s'], "׳") || strpos($q['s'], "'")) {
            $two_options_for_title = false;
            $has_apostrophes = true;
            foreach ($apostrophes_types as $apostrophes_type) {
                $other_options = handle_apostrophes_on_title($apostrophes_type, $term, true);
                if ($other_options != false) {
                    $two_options_for_title = true;
                    break;
                }
            }
        }
        if ($is_user || $is_ingredient) {
            if ($index == $users_amount - 1) {
                $term = esc_sql($wpdb->esc_like($term));
                if ($two_options_for_title) {
                    $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}'" . $other_options . "))";
                } else {
                    $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}'))";
                }
                $searchand = ' AND ';
            } else {
                $term = esc_sql($wpdb->esc_like($term));
                if ($two_options_for_title) {
                    $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}'" . $other_options . ")";
                } else {
                    $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
                }
                $searchand = ' AND ';
            }
            $index++;
        } else {
            $term = esc_sql($wpdb->esc_like($term));
            if ($two_options_for_title && is_search()) {
                if (count((array)$q['search_terms']) - 1 == $counter) {
                    $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}'" . $other_options . ")";

                } else {
                    $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
                }
            } else {
                $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
            }
            $searchand = ' AND ';
        }

        $counter++;
    }

    if (!empty($search)) {
        $search = " AND ({$search}) ";
        if (!is_user_logged_in()) {
            $search .= " AND ($wpdb->posts.post_password = '') ";
        }
    }

    return $search;
}

add_filter('posts_search', '__search_by_title_only', 500, 2);

function find_posts_by_title_and_type($titles, $post_type, $is_autocomplete, $has_wildcard = false)
{
    global $wpdb;
    $two_options_for_title = false;
    $apostrophes_types = ["’", "׳", "'"];
    if ($is_autocomplete) {
        foreach ($apostrophes_types as $apostrophes_type) {
            $other_options = handle_apostrophes_on_title($apostrophes_type, $titles, true);
            if ($other_options != false) {
                $two_options_for_title = true;
                break;
            }
        }

        if ($two_options_for_title) {
            $title = esc_sql($titles);
            $query = "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' and (post_title like '%$title%'" . $other_options . ") and post_type = '$post_type'";
        } else {
            $title = esc_sql($titles);
            $query = "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' and post_title like '%$titles%' and post_type = '$post_type'";
        }
    } else {
        foreach ($apostrophes_types as $apostrophes_type) {
            $other_options = handle_apostrophes_on_title($apostrophes_type, $titles, false);
            if ($other_options != false) {
                $two_options_for_title = true;
                break;
            }
        }
        if ($post_type == 'foody_ingredient') {
            $titles = esc_sql($titles);
            $titles = '%' . $titles . '%';
        }
        if ($two_options_for_title) {
            $query = "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' and (post_title like '$titles'" . $other_options . " ) and post_type = '$post_type'";
        } else {
            $query = "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' and post_title like '$titles' and post_type = '$post_type'";
        }
    }

    $results = $wpdb->get_results($query);

    return $results;
}

function handle_apostrophes_on_title($apostrophes_type, $title, $is_autocomplete)
{
    $other_title_options = "";
    $apostrophes_to_add = [];
    $wild_card = $is_autocomplete ? '%' : '';

    switch ($apostrophes_type) {
        case "'":
            $apostrophes_to_add = ["׳", "’"];
            break;
        case "׳":
            $apostrophes_to_add = ["'", "’"];
            break;
        case "’":
            $apostrophes_to_add = ["׳", "'"];
            break;
    }

    if (strpos($title, $apostrophes_type)) {
        foreach ($apostrophes_to_add as $apostrophe_to_add) {

            $title = esc_sql($title);

            $other_title_options .= ' or post_title like "' . $wild_card . str_replace($apostrophes_type, $apostrophe_to_add, $title) . $wild_card . '"';
        }
        return $other_title_options;
    } else {
        return false;
    }
}

function check_feed_areas_suggetions($search)
{
    $feed_areas_search_words = Foody_Query::get_feed_areas_search_words();

    $found_feed_areas = [];
    foreach ($feed_areas_search_words as $feed_area => $feed_area_search_words) {
        if (isset($feed_area_search_words) && is_array($feed_area_search_words)) {
            foreach ($feed_area_search_words as $word) {
                if (strpos($word['search_word'], $search) !== false) {
                    array_push($found_feed_areas, $feed_area);
                    break;
                }
            }
        }
    }

    $feed_areas_for_auto = [];
    if (!empty($found_feed_areas)) {
        foreach ($found_feed_areas as $feed_area_name) {
            $feed_area_to_add = find_posts_by_title_and_type($feed_area_name, 'foody_feed_channel', false);
            if (is_array($feed_area_to_add) && count($feed_area_to_add) > 0) {
                array_push($feed_areas_for_auto, [
                    'name' => clean_string_before_search($feed_area_to_add[0]->post_title),
                    'link' => Foody_Query::get_search_url(clean_string_before_search($feed_area_to_add[0]->post_title)),
                    'type' => 'feed_channel'
                ]);
            }
        }

    }
    return $feed_areas_for_auto;
}

function clean_string_before_search($search)
{
    $special_chars = ['#', '(', ')'];
    $cleaned_search = $search;
    foreach ($special_chars as $special_char) {
        $cleaned_search = str_replace($special_char, '', $cleaned_search);
    }

    return $cleaned_search;
}