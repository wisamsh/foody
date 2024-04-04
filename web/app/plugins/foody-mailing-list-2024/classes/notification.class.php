<?php

class Foody_notification
{
    // Constructor
    public function __construct()
    {

        //register_activation_hook(__FILE__, array($this, 'notification_tbl'));
        $this->notification_tbl();
        add_action('wp_footer', array($this, 'Takerecipe'));
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


public function regist_foody_notifiction_js(){
    wp_register_script('FoodyNotifictionSCRIPT', plugins_url('assets/js/foody-notification.js', __FILE__), array('jquery'), '1.0', true);

    // Enqueue the registered script
    wp_enqueue_script('FoodyNotifictionSCRIPT');
}


public function Takerecipe(){
    if (is_singular() && get_post_type() === 'foody_recipe') {
        add_action('wp_enqueue_scripts', array($this, 'regist_foody_notifiction_js'));

    }
   
}




} //END CLASS   
