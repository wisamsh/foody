<?php 
if (!defined('ABSPATH')) exit;

class Foody_Notofication_Extended
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'custom_publish_addNotificationToRecipe_meta_box'));
    }

   public function custom_publish_addNotificationToRecipe_meta_box() {
        global $post;
   
        // Check if the current post type is 'recipe'
        if ($post->post_type == 'foody_recipe') {
            add_meta_box(
                'NotificationToRecipe_meta_box', 
                'הכנס לטבלת שליחת התראות', 
                 array($this,'addNotificationToRecipe_callback'), 
                'foody_recipe', 
                'side', 
                'high'
            );
        }
       
    }

    public function addNotificationToRecipe_callback() {
        global $post;
      
    if($post->post_title && !$this->CheckRecepiesToSend($post->ID) ){
        echo '<div style="margin-top:10px;">
                <button type="button" class="button button-primary" id="custom_button">
              שמור ושלח למערכת התראות
                </button>
              </div>';
    }
   
            }



            function CheckRecepiesToSend($postID){

                global $wpdb;
                $table_name = $wpdb->prefix . 'notification_recipes_to_send';
                $sqlQuery = "SELECT * FROM {$table_name} WHERE recipe_id = {$postID}";
                $res = $wpdb->query($sqlQuery);
                return $res;
            }
            






        }//END CLASS

?>