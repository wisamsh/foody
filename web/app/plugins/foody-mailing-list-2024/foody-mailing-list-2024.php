<?php
/*
Plugin Name: Foody Mailing List Notification 2024
Description: This plugin is made for foody.co.il only.
Author: Wisam Shomar
*/

// Hook my_plugin_code() function into wp_loaded action

add_action('wp_loaded', 'my_plugin_code');

function my_plugin_code(){
    // Your code here that should run after WordPress core is loaded
    // This could include functions, actions, or any other logic
    echo get_the_ID();
die();
    // Include the class file
    require_once( __DIR__ . '/classes/notification.class.php');

    // Enqueue the script when URI contains 'foody_recipe'
    add_action('wp_enqueue_scripts', 'register_foody_notification_js');

function register_foody_notification_js() {
    if (strpos($_SERVER['REQUEST_URI'], 'foody_recipe') !== false) {
        $js_file_path = plugin_dir_path(__FILE__) . 'assets/js/foody-notification.js';
        wp_enqueue_script('foody-notification-script', plugins_url('assets/js/foody-notification.js', __FILE__), array(), '1.0', true);
    }
}


    // Instantiate the Foody_notification class
    $foodynotification = new Foody_notification();
}
?>
