<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/24/19
 * Time: 7:00 PM
 */

class Foody_CommercialRuleMapping
{
    public static $table_name;

    public static function createTable()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'foody_commercial_rule_mapping';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE " . self::$table_name . "  (
              `ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
              `rule_id` BIGINT(20) NOT NULL,
              `recipe_id` BIGINT(20) NOT NULL,
              `object_id` BIGINT(20) NOT NULL";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function add($rule_id, $recipe_id, $object_id)
    {
        global $wpdb;
        $result = $wpdb->insert(self::$table_name, [
            'rule_id' => $rule_id,
            'recipe_id' => $recipe_id,
            'object_id' => $object_id
        ]);

        if ($result === false) {
            Foody_WhiteLabelLogger::error("Error inserting to foody_commercial_rule_mapping: $wpdb->last_error", $wpdb->last_result);
        }

        return $result;
    }

    public static function remove($id)
    {
        global $wpdb;
        return $wpdb->delete(self::$table_name, [
            'id' => $id
        ]);
    }

    public static function getByPost($rule_id)
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT * from " . self::$table_name . " where rule_id = $rule_id", ARRAY_A);

        return $results;
    }

}