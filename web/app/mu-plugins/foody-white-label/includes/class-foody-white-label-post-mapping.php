<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/24/19
 * Time: 7:00 PM
 */

class Foody_WhiteLabelPostMapping
{
    public static $table_name;

    public static function createTable()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'foody_post_mapping';
        $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE " . self::$table_name . "  (
              `ID` BIGINT(20) NOT NULL,
              `post_id` BIGINT(20) NOT NULL,
              `blog_id` BIGINT(20) NOT NULL,
              `source` BIGINT(20) NULL,
              `source_type` VARCHAR(20) NULL,
              PRIMARY KEY (`ID`)) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    public static function add($post_id, $blog_id)
    {
        global $wpdb;
        return $wpdb->insert(self::$table_name, [
            'post_id' => $post_id,
            'blog_id' => $blog_id
        ]);
    }

    public static function remove($post_id, $blog_id)
    {
        global $wpdb;
        return $wpdb->delete(self::$table_name, [
            'post_id' => $post_id,
            'blog_id' => $blog_id
        ]);
    }

    public static function getByPost($post_id)
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT * from " . self::$table_name . " where post_id = $post_id", ARRAY_A);

        return $results;
    }

    public static function insertManyToBlog($posts, $blog_id)
    {
        global $wpdb;
        $values = array();
        $place_holders = array();

        $query = "INSERT INTO " . self::$table_name . " (post_id, blog_id) VALUES ";

        foreach ($posts as $post) {
            array_push($values, $post, $blog_id);
            $place_holders[] = "(%d, %d)";
        }

        $query .= implode(', ', $place_holders);
        return $wpdb->query($wpdb->prepare("$query ", $values));
    }
}