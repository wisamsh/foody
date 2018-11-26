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
 * @param string $where sql where query
 * @return mixed
 */
function foody_where_filter($where)
{
    global $wpdb;
    if (is_search()) {
        $search = get_search_query();
        $search = $wpdb->esc_like($search);
//        $search = esc_sql($search);
        $author_id = foody_search_user_by_name($search);

        if ($author_id) {
            $author_search = "($wpdb->posts.post_author = {$author_id})";
            $where_replace = " AND (($wpdb->posts.post_title";
            $replace_count = 1;
            $where = str_replace($where_replace, " AND ( $author_search OR ($wpdb->posts.post_title", $where, $replace_count);
        }

    }

    return $where;
}

add_filter('posts_where', 'foody_where_filter');


/**
 * @param string $name user name to search
 * @param bool $single whether to search fo a single user or multiple
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



    $result = array_map(function ($user) {
        return $user->user_id;
    }, $result);

    $ids = implode(',',$result);

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

    return $result;
}