<?php
if (!defined('ABSPATH')) exit;

class Foody_Notofication_Extended
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'custom_publish_addNotificationToRecipe_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_script_forAjaxNotificationAddToTable'));
        add_action('wp_ajax_handle_notification_table_update', array($this, 'handle_notification_table_update_ajax'));
    }

    public function custom_publish_addNotificationToRecipe_meta_box()
    {
        global $post;

        // Check if the current post type is 'recipe'
        if ($post->post_type == 'foody_recipe') {
            add_meta_box(
                'NotificationToRecipe_meta_box' . $post->ID,
                'טבלת שליחת התראות',
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

        if ($post->post_title && $this->CheckRecepiesToSend($post->ID)) {
            echo "<div style='text-align:center;margin-top:10px;' >
                   <b> <p style='color:red;' class='notice notice-warning'>מתכון זה נמצא במערכת שליחת התראות </p></b>
                <button type='button' class='button button-primary' id='update_notificationTable' data-postid = '{$post->ID}'>
              מחק ממערכת התראות
                </button>
              </div>";
        } else {
            echo "
              <b> <p style='color:red;' class='notice notice-info'>מתכון זה אינו נמצא במערכת שליחת התראות </p></b>
                  
            ";
        }
    }


    function enqueue_script_forAjaxNotificationAddToTable()
    {
        wp_enqueue_script('AjaxNotificationAddToTable', get_template_directory_uri() . '/resources/js/notification_extended.js', array('jquery'), null, true);

        // Localize the script with the AJAX URL and a nonce for security
        wp_localize_script('AjaxNotificationAddToTable', 'notification_table_nonce', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('notification_table_nonce') // Add security nonce
        ));
    }


    private function DeleteRecipeFromAlertTable($postID)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_recipes_to_send';
        $sqlQuery = sprintf("DELETE FROM {$table_name} where recipe_id = %d", $postID);
        $result = $wpdb->query($sqlQuery);
        return $result;
    }


    function handle_notification_table_update_ajax()
    {
        // Verify the nonce for security
        check_ajax_referer('notification_table_nonce', 'nonce');

        // Process the AJAX request
        $post_ID = $_POST['postID'];
        $res = $this->DeleteRecipeFromAlertTable($post_ID);
        $response = array(
            'message' => $res ? 'נמחק' : 'ישנה בעיה במחיקה',
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
