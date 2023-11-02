<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/24/19
 * Time: 7:00 PM
 */

class Foody_WhiteLabelPostMapping {
	public static $table_name;

	public static function createTable() {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'foody_post_mapping';
		$charset_collate  = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE " . self::$table_name . "  (
              `ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
              `post_id` BIGINT(20) NOT NULL,
              `destination_post_id` BIGINT(20) NOT NULL,
              `blog_id` BIGINT(20) NOT NULL,
              `source` BIGINT(20) NULL,
              `source_type` VARCHAR(20) NULL,
              PRIMARY KEY (`ID`)) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public static function add( $post_id, $destination_id, $blog_id ) {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'foody_post_mapping';
		$result = $wpdb->insert( self::$table_name, [
			'post_id'             => $post_id,
			'destination_post_id' => $destination_id,
			'blog_id'             => $blog_id
		] );

		if ( $result === false ) {
			Foody_WhiteLabelLogger::error( "Error inserting to foody_post_mapping: $wpdb->last_error", $wpdb->last_result );
		}

		return $result;
	}

	public static function remove( $post_id, $blog_id ) {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'foody_post_mapping';
		return $wpdb->delete( self::$table_name, [
			'post_id' => $post_id,
			'blog_id' => $blog_id
		] );
	}

	public static function getByPost( $post_id ) {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'foody_post_mapping';
		$results = $wpdb->get_results( "SELECT * from " . self::$table_name . " where post_id = $post_id", ARRAY_A );

		return $results;
	}

	public static function existsInBlog( $post_id, $blog ) {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'foody_post_mapping';
		$results = $wpdb->get_results( "SELECT * from " . self::$table_name . " where post_id = $post_id and blog_id = $blog", ARRAY_A );

		return ! empty( $results );
	}

	public static function insertManyToBlog( $posts, $blog_id ) {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'foody_post_mapping';
		$values        = array();
		$place_holders = array();

		$query = "INSERT INTO " . self::$table_name . " (post_id, blog_id) VALUES ";

		foreach ( $posts as $post ) {
			array_push( $values, $post, $blog_id );
			$place_holders[] = "(%d, %d)";
		}

		$query .= implode( ', ', $place_holders );

		return $wpdb->query( $wpdb->prepare( "$query ", $values ) );
	}
}