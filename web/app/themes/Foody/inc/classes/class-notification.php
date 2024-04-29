<?php
class Foody_Notification
{
    private $use_agreement_url;
    private $use_agreement_text;
    private $group_nots;
    private  $api_key;
    private $SmoovListName;

    function __construct()
    {
        $this->group_nots = array();

        $this->use_agreement_url = get_field('use_agreement_url', 'option');
        $this->use_agreement_text = get_field('use_agreement_text', 'option');
        $this->group_nots['main_title'] = get_field('main_title', 'option');
        $this->group_nots['second_title'] = get_field('second_title', 'option');
        $this->group_nots['agree_for_use_validation'] = get_field('agree_for_use_validation', 'option');
        $this->group_nots['missing_email'] = get_field('missing_email', 'option');
        $this->group_nots['email_exisit'] = get_field('email_exisit', 'option');
        $this->group_nots['success_regist'] = get_field('success_regist', 'option');
        $this->api_key = 'eaa83919-7ccc-4808-bd74-0b4e7c014dba';
        $this->SmoovListName = 'Notification-' . date('d-m-Y');

        $this->Creat_Necessary_Tables();
        $this->Creat_Necessary_Tables_smoov();
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
        $user_subscribe = $_POST['user_subscribe'];
        $author_name = $_POST['author_name'];
        $author_id = $_POST['author_id'];

        if (!$user_subscribe) {
            print_r($this->ErrorHandle(array("error" => "1", "reaseon" => "יש להסכים לתנאי שימוש!")));
            exit;
        }

        if ($cat_id == '' &&  $author_id == '') {
            print_r($this->ErrorHandle(array("error" => "1", "reaseon" => "יש לבחור קטגוריה או כותב!")));
            exit;
        }

        if ($email == '') {
            print_r($this->ErrorHandle(array("error" => "1", "reaseon" => "חסר אימייל!")));
            exit;
        }

        $email_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE email = %s and (category_id = %s and author_id = %s)",
            $email,
            $cat_id,
            $author_id

        ));

        if ($email_exists > 0) {
            // Email already exists in the database
            print_r($this->ErrorHandle(array("error" => "1", "reaseon" =>  $this->group_nots['email_exisit'])));

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
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'date_of_regist' => date("d-m-Y"),
                'user_subscribe' => $user_subscribe,
                'author_name' => $author_name,
                'author_id' => $author_id

            );


            $result = $wpdb->insert($table_name, $data);

            if ($result === false) {
                // There was an error with the insert operation
                print_r($this->ErrorHandle(array("error" => "1", "reaseon" => $wpdb->last_error)));
            } else {

                $smoov = $this->UpdateSmoov($email, '918510');

                // Insert operation was successful
                print_r($this->ErrorHandle(array("error" => "0", "reaseon" => $this->group_nots['success_regist'], 'smoov' => $smoov)));
            }
        }





        exit;
    }



    private function Creat_Necessary_Tables()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users'; // Your table name

        // Check if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table doesn't exist, create it
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                first_name VARCHAR(255),
                last_name VARCHAR(255),
                phone VARCHAR(20),
                email VARCHAR(255) NOT NULL,
                category_id INT(11) NOT NULL,
                recipe_id INT(11) NOT NULL,
                category_name VARCHAR(255),
                recipe_name VARCHAR(255),
                valid_user VARCHAR(255),
                user_ip VARCHAR(255),
                date_of_regist VARCHAR(255),
                user_subscribe VARCHAR(255),
                author_id VARCHAR(255),
                author_name VARCHAR(255),

                PRIMARY KEY  (id)
            ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        // else {
        //     // Table exists, check if the new field needs to be added
        //     $column_name = 'author_name';
        //     $column_exists = $wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE '$column_name'");
        //     if (!$column_exists) {
        //         // Add the new field
        //         $sql = "ALTER TABLE $table_name ADD COLUMN $column_name VARCHAR(255) AFTER user_ip";
        //         $wpdb->query($sql);
        //     }
        // }
    }


    private function Creat_Necessary_Tables_smoov()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_smoov_lists'; // Your table name

        // Check if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table doesn't exist, create it
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                last_smoov_list VARCHAR(255),
                created_date VARCHAR(20),
                recipe_id VARCHAR(20),

                PRIMARY KEY  (id)
            ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        // else {
        //     // Table exists, check if the new field needs to be added
        //     $column_name = 'recipe_id';
        //     $column_exists = $wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE '$column_name'");
        //     if (!$column_exists) {
        //         // Add the new field
        //         $sql = "ALTER TABLE $table_name ADD COLUMN $column_name VARCHAR(255) AFTER created_date";
        //         $wpdb->query($sql);
        //     }
        // }
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
        $get_the_author_ID = get_the_author();
        return $Termrtn;
    }


    public function DrawHTMLbox_notification()
    {
        $author_id = get_the_author_meta('ID');

        // Get the author name
        $author_name = get_the_author_meta('display_name');
        $term = $this->get_Primary_Term();

        $rtn = '';
        $rtn .= '<div class="notificationBox">';
        $rtn .= '<img class="not_icon" src="' . $this->not_icon() . '"/>';
        $rtn .= '<h4>' . $this->group_nots['main_title'] . '</h4>';
        $rtn .= '<span>' . $this->group_nots['second_title'] . '</span>';
        $rtn .= '<form id="notification_form">
    
    <div class="term_add" id="term_add">
    <span  class="add_term_plus" id="add_term" data-id="' . $term['term_id'] . '" data-name="' . $term['term_Name'] . '">+</span>
    <span >' . $term['term_Name'] . '</span>
    </div>

    <div class="term_add" id="author_add">
    <span class="add_author" id="add_author" data-id="' . $author_id . '" data-name="' . $author_name . '">+</span>
    <span >' . $author_name . '</span>
    </div>
    <div class="formWrapper">
    
    <input type="email" name="email" id="email" class="not_email"/>
    
    <input type="submit" class="submit" value="שלח" />
    </div>
    <input type="checkbox" name="user_subscribe" id="user_subscribe" checked />
    <label for="user_subscribe">
    <a href="' . $this->use_agreement_url . '">' . $this->use_agreement_text . '<a/>
    </label>
    
    <input type="hidden" name="action" id="action" value="notification_action_call"/>

    <input type="hidden" name="cat_id" id="cat_id" value=""/>
    <input type="hidden" name="cat_name" id="cat_name" value=""/>
    <input type="hidden" name="author_id" id="author_id" value=""/>
    <input type="hidden" name="author_name" id="author_name" value=""/>
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
    border-radius: 5px;
    margin-top: 10px;
    margin-bottom:10px;
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
    .add_term_plus , .add_author {
    font-size: 30px;
    font-weight: bold;
    position: absolute;
    top: -1px;
    right: 4px;
    }
    .term_add{
        width:48%;
        position:relative;
        padding:10px;
        border:solid 1px #ddd;
        border-radius:20px;
        display: inline-block;
        margin-top:10px;
        cursor: pointer;
        text-align:center;

    }
    
    .term_add_picked{
        background: #08871b;
        color:#fff !important;
    }
    </style>
    ';
        return $rtn;
    }


    // Add a custom admin page
    public function add_admin_menu()
    {
        add_menu_page(
            'Notification Users',   // Page title
            'Notification Users',   // Menu title
            'manage_options',       // Capability required
            'notification_users',   // Menu slug
            array($this, 'draw_notification_users_admin_page'), // Callback function
            'dashicons-email',      // Icon URL or CSS class
            25                    // Menu position
        );

        add_submenu_page(
            'notification_users',          // Parent menu slug
            'Update Smoov Lists',          // Page title
            'Smoov Lists Update',               // Menu title
            'manage_options',              // Capability required
            'notification_users_submenu', // Submenu slug
            array($this, 'GetSmoovListsUpdated') // Callback function
        );
    }



    public function GetSmoovListsUpdated()
    {
        global $wpdb;

        if (!isset($_REQUEST['smoovListUpdate'])) {
            if (empty($this->check_Last_smoovList())){
?>
            <div style="width:100%;padding:20px;margin-top:30px;">
                <div class="notice notice-success">
                    <p>The New Smoov List Would Be Called : <?php echo $this->SmoovListName; ?></p>
                </div>

                <form id="smoovupdateform" method="post" style="padding:20px;margin-top:30px;">
                    <input type="submit" class="button button-primary" value="Create List - <?php echo $this->SmoovListName ?>" />
                    <input type="hidden" id="smoovListUpdate" name="smoovListUpdate" />
                </form>
            </div>
        <?php
            }
        } else {
            echo $this->makeSmoovCategoriesList();
        }
    } //end GetSmoovListsUpdated function


    // Render admin page
    public function draw_notification_users_admin_page()
    {
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
        <script>
            function validate(form) {

                return confirm('בטוח למחוק?');

            }
        </script>
        <div class="wrap">
            <h1>Notification Users</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>קטגוריה</th>
                        <th>כותב</th>
                        <th>מתכון</th>
                        <th>אימייל</th>
                        <th>ip לקוח</th>
                        <th>תאריך רישום </th>
                        <th>הסכים לתנאי שימוש</th>
                        <th>Action</th> <!-- New column for delete button -->
                        <!-- Add more table headers as needed -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row) : ?>
                        <tr>
                            <td><?php echo $row->id; ?></td>
                            <td><?php echo $row->category_name; ?></td>
                            <td><?php echo $row->author_name; ?></td>
                            <td><?php echo $row->recipe_name; ?></td>
                            <td><?php echo $row->email; ?></td>
                            <td><?php echo $row->user_ip; ?></td>
                            <td><?php echo $row->date_of_regist; ?></td>
                            <td><?php echo $row->user_subscribe == 'on' ? 'כן' : '' ?></td>
                            <td>
                                <form method="post" onsubmit="return validate(this);">
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


    public function Delete_handle_delete_notification_user()
    {
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

    private function GetSmoovList($list_id)
    {


        //$url = 'https://rest.smoove.io/v1/Lists/id?='..'&api_key=' . $this->api_key;;
    }





    private function UpdateSmoov($email, $listID)
    {


        $url = 'https://rest.smoove.io/v1/Contacts?updateIfExists=true&api_key=' . $this->api_key;

        // Parameters for the API request
        $params = array(

            'lists_ToSubscribe' => array($listID),  // Replace 'LIST_ID_HERE' with the ID of your list
            'email' => $email,
            'customFields' => array(
                'date_of_regitration' => date("d-m-Y"),
            )  // Email address to add to the list
        );

        // Initialize cURL session
        $ch = curl_init($url);

        // Set the POST data
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));


        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $response = curl_exec($ch);

        // Get the HTTP response code
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($ch);
        return $http_status;
    }






    private function makeSmoovCategoriesList()
    {
        $url = 'https://rest.smoove.io/v1/Lists?api_key=' . $this->api_key;

        // Parameters for the API request
        $params = array(
            'name' => $this->SmoovListName,
            'publicName' => $this->SmoovListName,
            'description' => 'To be removed after this date ' . date('d-m-Y'),
            'permissions' => array(
                'isPublic' => false,

            )
        );

        // Initialize cURL session
        $ch = curl_init($url);

        // Set the POST data
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));


        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $response = curl_exec($ch);

        // Get the HTTP response code
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($ch);
        if ($http_status == 200) {
            
            return  '<div class="notice notice-success"><p>List Created Smoov status:' . $http_status .
            $this->inserLast_db_smoov_list($this->SmoovListName, $post_id = null) .
            '</p></div>';
        } else {
            return  '<div class="notice notice-success"><p>Smothing went wrong :  ' . $http_status . '</p></div>';
        }
    } //end  makeSmoovCategoriesList()


    private function getlast_db_smoov_list()
    {
        global $wpdb;

        // Replace 'your_table_name' with the actual name of your table
        $table_name = $wpdb->prefix . 'notification_smoov_lists';

        // Fetch the last record
        $last_record = $wpdb->get_row("SELECT * FROM $table_name ORDER BY id DESC LIMIT 1");
        return $last_record;
    }

    private function inserLast_db_smoov_list($name, $recipe_id)
    {
        global $wpdb;

        // Replace 'your_table_name' with the actual name of your table
        $table_name = $wpdb->prefix . 'notification_smoov_lists';

        $data_to_insert = array(
            'last_smoov_list' => $name,
            'created_date' => date('d-m-y'),
            'recipe_id'=> $recipe_id
            // Add more columns and values as needed
        );
        
        // Insert data into the table
        $result = $wpdb->insert($table_name, $data_to_insert);
        
        // Check if the insertion was successful
        if ($result === false) {
            // Insertion failed
            return " Error: " . $wpdb->last_error;
        } else {
            // Insertion successful
            return " Record inserted successfully";
        }

    } // end inserLast_db_smoov_list()



    private function check_Last_smoovList() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_smoov_lists';
        $field = $this->SmoovListName;
        $Sql = $wpdb->prepare('SELECT * FROM ' . $table_name  . ' WHERE last_smoov_list = %s', $field);
        $result = $wpdb->get_results($Sql);
        return $result;
    }
    
    public function on_SavingRecipe($post_id, $post, $update){
        if (wp_is_post_revision($post_id)) {
            return;
        }
    
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        // Check post type to make sure it's "recipe"
        if ($post->post_type !== 'foody_recipe') {
            return;
        }
    
        // Your code to handle save/update action for recipe posts goes here
    
        
        $recipe_name = get_post_meta($post_id, 'recipe_name', true);
      
        if(empty($this->check_Last_smoovList())){
        $this->makeSmoovCategoriesList();

       }
    }



}//end class
