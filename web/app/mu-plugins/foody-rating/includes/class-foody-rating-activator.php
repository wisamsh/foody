<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/zariMatan
 * @since      1.0.0
 *
 * @package    Foody_Rating
 * @subpackage Foody_Rating/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Foody_Rating
 * @subpackage Foody_Rating/includes
 * @author     Matan Zari <zari@moveo.co.il>
 */
class Foody_Rating_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        self::create_rating_table();
    }


    public static function create_rating_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'foody_rating';


        $sql = "
              CREATE TABLE $table_name (
              ID BIGINT(20) NOT NULL AUTO_INCREMENT,
              user_id BIGINT(20) NOT NULL,
              post_id BIGINT(20) NOT NULL,
              post_type VARCHAR(20) NOT NULL,
              value DECIMAL(20,2) NOT NULL,
              PRIMARY KEY  (ID),
              UNIQUE KEY unique_user_rating (user_id, post_id)
              ) $charset_collate;
        ";


        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }

}
