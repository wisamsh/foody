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
 */function foody_where_filter($where)
{
    global $wpdb;
    if (is_search()) {
        $search = get_query_var('s');
        $query = $wpdb->prepare("SELECT user_id  FROM $wpdb->usermeta WHERE ( meta_key='first_name' AND meta_value LIKE '%%%s%%' ) or ( meta_key='last_name' AND meta_value LIKE '%%%s%%' )", $search, $search);
        $authorID = $wpdb->get_var($query);

        if ($authorID) {
            $author_search = "($wpdb->posts.post_author = {$authorID})";
            $where_replace = " AND (($wpdb->posts.post_title LIKE '%$search%'))";
            $where = str_replace($where_replace," AND ( $author_search OR ($wpdb->posts.post_title LIKE '%$search%'))",$where);
        }

    }

    return $where;
}

add_filter('posts_where','foody_where_filter');