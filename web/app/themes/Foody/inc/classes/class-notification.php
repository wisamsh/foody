<?php
class Foody_Notification
{



    function __construct()
    {
        $this->Creat_Necessary_Tables();
        $this->enqueue_Notification_scripts();

       
    }

    
    private function ErrorHandle($err = [])
    {
        return json_encode($err);
    }


    public function not_icon()
    {
        return get_template_directory_uri() . '/resources/images/message_notification.png';
    }

    function get_Details()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users';
        //print_r($_POST);
        $email = $_POST['email'];
        $cat_id = $_POST['cat_id'];
        $cat_name = $_POST['cat_name'];
        $recipe_id = $_POST['recipe_id'];
        $recipe_name = $_POST['recipe_name'];

        if ($email == '') {
            print_r($this->ErrorHandle(array("error" => "1", "reaseon" => "חסר אימייל!")));
            exit;
        }
        $email_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE email = %s and category_id = %s",
            $email,
            $cat_id
        ));

        if ($email_exists > 0) {
            // Email already exists in the database
            print_r($this->ErrorHandle(array("error" => "1", "reaseon" => "המייל קיים בקטגוריה זאת")));

            exit;
        } else {


            $data = array(
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'email' => $email,
                'category_id' => $cat_id,
                'recipe_id' => $recipe_id,
                'category_name' => $cat_name,
                'recipe_name' => $recipe_name,
                'valid_user' => '',
                'user_ip' => $_SERVER['REMOTE_ADDR']

            );


            $result = $wpdb->insert($table_name, $data);

            if ($result === false) {
                // There was an error with the insert operation
                print_r($this->ErrorHandle(array("error" => "1", "reaseon" => $wpdb->last_error)));
            } else {
                // Insert operation was successful
                print_r($this->ErrorHandle(array("error" => "0", "reaseon" => "נקלט בהצלחה!")));
            }
        }





        exit;
    }



    private function Creat_Necessary_Tables()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users'; // Your table name
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            first_name VARCHAR(255) ,
            last_name VARCHAR(255) ,
            phone VARCHAR(20),
            email VARCHAR(255) NOT NULL,
            category_id INT(11) NOT NULL,
            recipe_id INT(11) NOT NULL,
            category_name VARCHAR(255),
            recipe_name VARCHAR(255),
            valid_user VARCHAR(255),
            user_ip VARCHAR(255),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $wpdb->query($sql);
    }



    public function get_Primary_Term()
    {
        $Termrtn = [];
        $foody_post = Foody_Post::create(get_post(), false);
        $cat = $foody_post->get_primary_category();
        $category = get_category($cat);
        $term_id = ($category->term_id);
        $term_Name = ($category->name);
        $Termrtn['term_id'] = $term_id;
        $Termrtn['term_Name'] = $term_Name;
        return $Termrtn;
    }


    public function DrawHTMLbox_notification()
    {
        $term = $this->get_Primary_Term();

        $rtn = '';
        $rtn .= '<div class="notificationBox">';
        $rtn .= '<img class="not_icon" src="' . $this->not_icon() . '"/>';
        $rtn .= '<h4>שלחו לי התראה</h4>';
        $rtn .= '<span>כשיש מתכון בקטגוריה : </span> <span><b>' .  $term['term_Name']   . '</b></span>';
        $rtn .= '<form id="notification_form">
    <div class="formWrapper"><input type="email" name="email" id="email" class="not_email"/>
    <input type="submit" class="submit" value="שלח"></div>
    <input type="hidden" name="action" id="action" value="notification_action_call"/>

    <input type="hidden" name="cat_id" id="cat_id" value="' . $term['term_id'] . '"/>
    <input type="hidden" name="cat_name" id="cat_name" value="' . $term['term_Name'] . '"/>
    <input type="hidden" name="recipe_id" id="recipe_id" value="' . get_the_ID() . '"/>
    <input type="hidden" name="recipe_name" id="recipe_name" value="' . get_the_title() . '"/>
    <input type="hidden" name="recipe_name" id="recipe_name" value="' . get_the_title() . '"/>
    
    </form>';
        $rtn .= '<p id="notification_ajax_response"></p></div>';

        return $rtn;
    }

    public function enqueue_Notification_scripts()
    {
        // Enqueue your script
        wp_enqueue_script('notification-script', get_template_directory_uri() . '/components/js/notification.js', array(), '1.0.0', true);
        wp_localize_script('notification-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_Notification_scripts'));
    }


    public function handle_ajax()
    {
        // Your AJAX handling logic here
        $data = $_POST;

        // Process your data or perform any other actions

        // Send a response (example response)
        $response = 'Data received: ' . $data;

        //wp_send_json_success($response); // Send success response
        print_r($response);
    }






    public function DrawCSS_Notification()
    {
        $rtn = '
    <style>
.formWrapper{
    border: solid 1px #ddd;
    background: #589fba;
    /* padding-top: 5px; */
    /* padding-bottom: 5px; */
    border-radius: 5px;
    margin-top: 10px;
}
.not_icon{
    width: 30px;
    position: absolute;
    left: 9px;
    top: 3px;
}

    .notificationBox{
    position:relative;
    max-width: 400px;
    margin: 0 auto;
    text-align: right;
    margin-top: 20px;
    margin-bottom: 20px;
    border: solid 1px #589fba4d;
    border-radius: 5px;
    padding: 10px;
    }
    .not_email{
        width: 76%;
    border: solid 0px !important;
    box-shadow: none !important;
    }
    .submit{
        width: 22%;
        border: solid 0px !important;
        background: none !important;
        border-bottom-left-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
        font-size:14px !important;
        font-weight:bold;
        color:#fff !important;
    }
    </style>
    ';
        return $rtn;
    }


// Add a custom admin page
public function add_admin_menu() {
    add_menu_page(
        'Notification Users',   // Page title
        'Notification Users',   // Menu title
        'manage_options',       // Capability required
        'notification_users',   // Menu slug
        array($this, 'draw_notification_users_admin_page'), // Callback function
        'dashicons-email',      // Icon URL or CSS class
       25                    // Menu position
    );
}

// Render admin page
public function draw_notification_users_admin_page() {
    global $wpdb;
    
    // Items per page
    $per_page = 40;
    
    // Current page number
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    
    // Offset calculation
    $offset = ($current_page - 1) * $per_page;

    // Fetch total number of notification users
    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}notification_users");
    
    // Fetch data from the wp_notification_users table
    $table_name = $wpdb->prefix . 'notification_users';
    $data = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    // Output the data in a table format
    ?>
    <div class="wrap">
        <h1>Notification Users</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>קטגוריה</th>
                    <th>מתכון</th>
                    <th>אימייל</th>
                    <th>ip לקוח</th>
                    <th>Action</th> <!-- New column for delete button -->
                    <!-- Add more table headers as needed -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $row->id; ?></td>
                    <td><?php echo $row->category_name; ?></td>
                    <td><?php echo $row->recipe_name; ?></td>
                    <td><?php echo $row->email; ?></td>
                    <td><?php echo $row->user_ip; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="delete_notification_user">
                            <input type="hidden" name="user_id" value="<?php echo $row->id; ?>">
                            <button type="submit" class="button button-primary">מחק</button>
                        </form>
                    </td>
                    <!-- Add more table cells for additional columns -->
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php
        $page_links = paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo; Previous'),
            'next_text' => __('Next &raquo;'),
            'total' => ceil($total_items / $per_page),
            'current' => $current_page,
        ));
        
        if ($page_links) {
            echo '<div class="tablenav" style="width:100%;text-align:center;"><div style="width:100%;text-align:center;" class="tablenav-pages">' . $page_links . '</div></div>';
        }
        ?>
    </div>
    <?php
}


public function Delete_handle_delete_notification_user() {
    global $wpdb;

    // Check if the request is a POST request and if the action is to delete a notification user
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_notification_user') {
        // Retrieve the user ID to be deleted
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

        // Validate the user ID
        if ($user_id > 0) {
            // Delete the user from the database
            $table_name = $wpdb->prefix . 'notification_users';
            $result = $wpdb->delete($table_name, array('id' => $user_id));

            // Check if the delete operation was successful
            if ($result !== false) {
                echo '<div class="notice notice-success"><p>User deleted successfully.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Error deleting user.</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>Invalid user ID.</p></div>';
        }
    }
}




}//end class
