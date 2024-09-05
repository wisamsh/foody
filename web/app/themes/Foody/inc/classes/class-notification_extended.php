<?php
if (!defined('ABSPATH')) exit;

class Foody_Notofication_Extended
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'custom_publish_addNotificationToRecipe_meta_box'));
        add_action('admin_enqueue_scripts', array($this,'enqueue_script_forAjaxNotificationAddToTable'));
        add_action('wp_ajax_handle_notification_table_update', array($this,'handle_notification_table_update_ajax'));
    }

    public function custom_publish_addNotificationToRecipe_meta_box()
    {
        global $post;

        // Check if the current post type is 'recipe'
        if ($post->post_type == 'foody_recipe') {
            add_meta_box(
                'NotificationToRecipe_meta_box',
                'הכנס לטבלת שליחת התראות',
                array($this, 'addNotificationToRecipe_callback'),
                'foody_recipe',
                'side',
                'high'
            );
        }
    }

    public function addNotificationToRecipe_callback()
    {
        global $post;

        if ($post->post_title && !$this->CheckRecepiesToSend($post->ID)) {
            echo '<div style="margin-top:10px;">
                <button type="button" class="button button-primary" id="update_notificationTable">
              שמור ושלח למערכת התראות
                </button>
              </div>';
        }
    }

   
function enqueue_script_forAjaxNotificationAddToTable() {
    wp_enqueue_script('AjaxNotificationAddToTable', get_template_directory_uri() . '/resources/js/notification_extended.js', array('jquery'), null, true);
    
    // Localize the script with the AJAX URL and a nonce for security
    wp_localize_script('AjaxNotificationAddToTable', 'notification_table_nonce', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('notification_table_nonce') // Add security nonce
    ));
}


function handle_notification_table_update_ajax() {
    // Verify the nonce for security
    check_ajax_referer('notification_table_nonce', 'nonce');
    
    // Process the AJAX request
    $response = array(
        'message' => 'Custom button AJAX request successful!'
    );
    
    // Send the response back to the JavaScript function
    wp_send_json_success($response);
}


    function CheckRecepiesToSend($postID)
    {

        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_recipes_to_send';
        $sqlQuery = "SELECT * FROM {$table_name} WHERE recipe_id = {$postID}";
        $res = $wpdb->query($sqlQuery);
        return $res;
    }








} //END CLASS
