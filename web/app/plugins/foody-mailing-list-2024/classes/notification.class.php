<?php

class Foody_notification
{
    // Constructor
    public function __construct()
    {
        
        $this->notification_tbl();

       
       if(function_exists("regist_foody_notifiction_js")){
        add_action('wp_enqueue_scripts', 'regist_foody_notifiction_js');
       }


    }

    // Function to create custom table
    private function notification_tbl()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users'; // Your table name
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                first_name VARCHAR(255) NOT NULL,
                last_name VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                email VARCHAR(255) NOT NULL,
                category_id INT(11) NOT NULL,
                recipe_id INT(11) NOT NULL,
                category_name VARCHAR(255),
                recipe_name VARCHAR(255),
                valid_user VARCHAR(255),
                PRIMARY KEY  (id)
            ) $charset_collate;";

        $wpdb->query($sql);
    }

    // Register the Foody notification JavaScript file
   

    // Enqueue the Foody notification JavaScript file if the current post type is 'foody_recipe'
    public function Takerecipe()
    {
        
    }
}

