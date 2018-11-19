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
        $search = get_query_var('s');

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


function foody_search_user_by_name($name,$single=true)
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

    $query = $wpdb->prepare($query, $args);

    if($single){
        $result = $wpdb->get_var($query);
    }else{
        $result = $wpdb->get_results($query);
    }

    return $result;
}