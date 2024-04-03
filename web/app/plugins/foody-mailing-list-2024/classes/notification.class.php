<?php 

class Custom_Table_Creator {
    // Constructor
    public function __construct() {
        register_activation_hook( __FILE__, array( $this, 'notification_tbl' ) );
    }

    // Function to create custom table
    public function notification_tbl() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users'; // Your table name

        // Check if table exists
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                first_name VARCHAR(255) NOT NULL,
                last_name VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                email VARCHAR(255),
                category_id INT(11),
                recipe_id INT(11),
                category_name VARCHAR(255),
                recipe_name VARCHAR(255),
                valid_user VARCHAR(255),
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

            dbDelta( $sql );

            // Add a note on top if the table is successfully created
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                echo '<p>Notification table created successfully!</p>';
            }
        }
    }
}

// Instantiate the class

?>
