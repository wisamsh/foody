<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/19/18
 * Time: 2:39 PM
 */


/**
 * Adds search by author name functionality
 * to default search
 *
 * @param string $where sql where query
 *
 * @return mixed
 */
function foody_where_filter($where)
{
    global $wpdb;
    if (is_search() || (!empty($_POST) && ((isset($_POST['action']) && $_POST['action'] == 'load_more') || (isset($_POST['action']) && $_POST['action'] == 'foody_filter')))) {
        $author_id = 0;
        $ingredients = [];
        if (isset($_POST['filter']) && isset($_POST['filter']['search'])) {
            $search = $_POST['filter']['search'];
        } elseif (isset($_POST['data']) && isset($_POST['data']['search'])) {
            $search = $_POST['data']['search'];
        } else {
            $search = get_search_query();
            $search = str_replace("&#039;", "'", $search);
        }
        if ($search != '') {
            if (get_page_by_title($search, OBJECT, 'post') || get_page_by_title($search, OBJECT, 'foody_recipe')) {
                $author_id = 0;
                $ingredients = [];
            } else {
                $author_id = foody_search_user_by_name($search);
                if (!$author_id) {
                    $ingredients = find_posts_by_title_and_type($search, 'foody_ingredient', false);
                    if (!empty($ingredients)) {
                        $ingredients = array_map(function ($post) {
                            return $post->post_title;
                        }, $ingredients);
                    }
                }
            }
            if (strstr($where, " AND (($wpdb->posts.post_title")) {
                if ($author_id) {
                    //if(!isset($_POST['action']) || $_POST['action'] !== 'load_more'){
                    $authors_feed_areas = get_feed_areas_from_author_name($search);
                    $feed_areas_search = '';
                    if (!empty($authors_feed_areas)) {
                        foreach ($authors_feed_areas as $index => $authors_feed_area) {
                            $feed_areas_search .= "($wpdb->posts.post_title = '{$authors_feed_area}') OR ";
                        }
                    }

                    $author_search = "($wpdb->posts.post_author = {$author_id})";
                    $where_replace = " AND (($wpdb->posts.post_title";
                    $replace_count = 1;
                    $where = str_replace($where_replace, " AND ( $author_search OR $feed_areas_search (($wpdb->posts.post_title", $where, $replace_count);
                } elseif (!empty($ingredients)) {
                    $esc_ingredient = esc_sql($ingredients[0]);
                    $ingredient_search = "SELECT post_id FROM {$wpdb->postmeta} as postmeta 
JOIN {$wpdb->posts} as posts
where posts.ID = postmeta.post_id 
	AND meta_key like 'ingredients_ingredients_groups_%_ingredients_%_ingredient'
	AND meta_value = (SELECT ID FROM {$wpdb->posts} where post_title = '$esc_ingredient' and post_status = 'publish' AND post_type = 'foody_ingredient')
    AND post_status = 'publish'
group by post_id ";
                    $where_replace = " AND (($wpdb->posts.post_title";
                    $replace_count = 1;
                    $where = str_replace($where_replace, " AND ( $wpdb->posts.ID IN ($ingredient_search) OR (($wpdb->posts.post_title", $where, $replace_count);
                }
            }
        }
    }
    return $where;
}

add_filter('posts_where', 'foody_where_filter');


function get_feed_areas_from_author_name($author)
{
    $found_feed_areas = [];
    $feed_areas_search_words = Foody_Query::get_feed_areas_search_words();
    foreach ($feed_areas_search_words as $feed_area => $feed_area_search_words) {
        if (isset($feed_area_search_words)) {
            foreach ($feed_area_search_words as $word) {
                if (strpos($word['search_word'], $author) !== false) {
                    array_push($found_feed_areas, $feed_area);
                    break;
                }
            }
        }
    }

    return $found_feed_areas;
}

/**
 * @param string $name user name to search
 * @param bool $single whether to search fo a single user or multiple
 *
 * @return array|null|object|string single user id or an array of user ids.
 */
function foody_search_user_by_name($name, $single = true)
{
    global $wpdb;

    $query =
        "SELECT user_id
              FROM $wpdb->usermeta 
              WHERE 
              ( meta_key='first_name' AND meta_value LIKE '%%%s%%' ) 
              or ( meta_key='last_name' AND meta_value LIKE '%%%s%%' )";

    $args = [
        $name,
        $name
    ];

    $name_parts = explode(' ', trim($name));


    if (count($name_parts) > 1) {
        $full_name_search = "

           ";

        $first_name = [];
        while (count($name_parts) > 1) {
            $first_name[] = array_shift($name_parts);

            $first_name_search = implode(' ', $first_name);
            $last_name_search = implode(' ', $name_parts);
            $full_name_search .= "
                   or ( meta_key='first_name' AND meta_value LIKE '%%$first_name_search%%' )
                   or ( meta_key='last_name' AND meta_value LIKE '%%$last_name_search%%' )
                ";
        }

        $query = "$query $full_name_search";
    }

    $query_authors = "AND user_id IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'wp_capabilities' AND meta_value = 'a:1:{s:6:\"author\";b:1;}') ";


    $query = $wpdb->prepare($query, $args);

    $result = $wpdb->get_results($query);


    if (!empty($result)) {
        $result = array_map(function ($user) {
            return $user->user_id;
        }, $result);

        $ids = implode(',', $result);

        $query = "SELECT user_id from $wpdb->usermeta where user_id IN ($ids) and meta_value LIKE '%\"author\"%'";

        if ($single) {
            $result = $wpdb->get_var($query);
        } else {
            $result = $wpdb->get_results($query);
            if (!empty($result) && is_array($result)) {
                $result = array_map(function ($user) {
                    return $user->user_id;
                }, $result);
            }
        }
    }


    return $result;
}


/**
 * Loads values from ACF field 'filters_list'
 * set in the custom options page 'Foody search options'
 *
 * @param $value mixed current field value
 *
 * @return mixed|null
 */
function foody_filters_acf_load_value($value)
{

    if (empty($value) && is_admin()) {

        $screen = get_current_screen();

        $relevant_screens = [
            'edit-category',
            'foody_channel',
            'user-edit',
            'edit-post_tag'
        ];

        if (in_array($screen->id, $relevant_screens)) {
            // no values set for filters lists,
            // load default values from global search settings (foody_search_settings)
            // see Sidebar_Filter for more details.
            remove_filter('acf/load_value/name=filters_list', 'foody_filters_acf_load_value', 1000);
            $global_search_settings = get_field('filters_list', 'foody_search_options', false);
            $value = $global_search_settings;
            add_filter('acf/load_value/name=filters_list', 'foody_filters_acf_load_value', 1000, 1);
        }
    }

    return $value;
}

add_filter('acf/load_value/name=filters_list', 'foody_filters_acf_load_value', 1000, 1);


/**
 *  Get the ACF post_id for the current
 * page, Defaults to Sidebar_Filter::FILTER_OPTIONS_ID
 * @return int|string
 */
function get_filters_id()
{
    // load filters specific to current page
    $id = get_queried_object_id();
    if (is_category() || is_tag()) {
        /** @var WP_Term $term */
        $term = get_queried_object();
        $filters_post_id = $term->taxonomy . '_' . $term->term_id;
    } elseif (is_author()) {
        $filters_post_id = "user_$id";
    } elseif (is_single() && get_post_type() == 'foody_channel') {
        $filters_post_id = $id;
    } else {
        $filters_post_id = SidebarFilter::FILTER_OPTIONS_ID;
    }

    if ($filters_post_id != SidebarFilter::FILTER_OPTIONS_ID) {
        if (!have_rows('filters_list', $filters_post_id)) {
            $filters_post_id = SidebarFilter::FILTER_OPTIONS_ID;
        }
    }

    return $filters_post_id;
}