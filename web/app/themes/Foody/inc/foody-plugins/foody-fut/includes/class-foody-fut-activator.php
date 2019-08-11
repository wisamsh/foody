<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/zariMatan
 * @since      1.0.0
 *
 * @package    Foody_Fut
 * @subpackage Foody_Fut/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Foody_Fut
 * @subpackage Foody_Fut/includes
 * @author     Matan Zari <zari@moveo.co.il>
 */
class Foody_Fut_Activator {

	private static $table_name = 'foody_fut';

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::create_fut_table();
	}


	public static function create_fut_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . self::$table_name;


		$sql = "
              CREATE TABLE $table_name (
              ID BIGINT(20) NOT NULL AUTO_INCREMENT,
              email VARCHAR(40) NOT NULL,
              PRIMARY KEY  (ID),
              UNIQUE KEY email (email)
              ) $charset_collate;
        ";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	private function restrict() {


	}

}
