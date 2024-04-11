<?php 
class Foody_Notification{

function __construct()
{
   $this->Creat_Necessary_Tables();
  $this->enqueue_Notification_scripts();
 }


function get_Details(){
    print_r($_POST['email']);
    exit;
}



private function Creat_Necessary_Tables(){
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



public function get_Primary_Term(){
    $Termrtn = [];
    $foody_post = Foody_Post::create(get_post(), false);
    $cat = $foody_post->get_primary_category();
    $category = get_category($cat);
    $term_id = ($category->term_id);
    $term_Name = ($category->name);
    $Termrtn['term_id'] =$term_id;
    $Termrtn['term_Name'] =$term_Name;
    return $Termrtn;

}


public function DrawHTMLbox_notification(){
   $term = $this->get_Primary_Term();
   
   $rtn = '';
    $rtn .= '<div class="notificationBox">';
    $rtn .='<h4>שלחו לי התראה</h4>';
    $rtn .= '<span>כשיש מתכון בקטגוריה : </span> <span><b>' .  $term['term_Name']   . '</b></span>';
    $rtn .= '<form id="notification_form"><div class="formWrapper"><input type="email" name="email" id="email"/>
    <input type="submit"></div>
    <input type="hidden" name="action" id="action" value="notification_action_call"/>
    </form>';
    $rtn .= '<p id="notification_ajax_response-response"></p></div>';
    return $rtn;

}

public function enqueue_Notification_scripts() {
    // Enqueue your script
    wp_enqueue_script( 'notification-script', get_template_directory_uri() . '/components/js/notification.js', array(), '1.0.0', true );
    wp_localize_script('notification-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_Notification_scripts' ) );
   
}


public function handle_ajax() {
    // Your AJAX handling logic here
    $data = $_POST['email'];

    // Process your data or perform any other actions
    
    // Send a response (example response)
    $response = 'Data received: ' . $data;

    //wp_send_json_success($response); // Send success response
   print_r( $response);
}






public function DrawCSS_Notification(){
    $rtn = '
    <style>
    .notificationBox{
    max-width: 400px;
    margin: 0 auto;
    text-align: right;
    margin-top: 20px;
    margin-bottom: 20px;
    border: solid 1px #589fba4d;
    border-radius: 5px;
    padding: 10px;
    }
    </style>
    ';
    return $rtn ;
}

}