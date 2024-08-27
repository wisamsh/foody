<?php
error_reporting(0);
if (!defined('ABSPATH')) exit;
class Foody_Notification
{
    private $use_agreement_url;
    private $use_agreement_text;
    private $group_nots;
    private  $api_key;
    private $email_Image_Header;
    private $EnvyormentType ;
    private $SmoovListName;

    function __construct()
    {

        $this->EnvyormentType =  $_SERVER['SERVER_NAME'] == '0.0.0' ? 'http://foody-local.co.il' : $_SERVER['SERVER_NAME'];

        $this->group_nots = array();

        $this->use_agreement_url = get_field('use_agreement_url', 'option');
        $this->use_agreement_text = get_field('use_agreement_text', 'option');
        $this->group_nots['main_title'] = get_field('main_title', 'option');
        $this->group_nots['second_title'] = get_field('second_title', 'option');
        $this->group_nots['agree_for_use_validation'] = get_field('agree_for_use_validation', 'option');
        $this->group_nots['missing_email'] = get_field('missing_email', 'option');
        $this->group_nots['email_exisit'] = get_field('email_exisit', 'option');
        $this->group_nots['success_regist'] = get_field('success_regist', 'option');
        $this->api_key = get_field("mailgun_api_key", "option");
        // $this->api_key = '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';
        $this->SmoovListName = 'Notification-' . date('d-m-Y');
        $this->email_Image_Header = 'https://foody-media.s3.eu-west-1.amazonaws.com/w_images/email/mail-header.png';
        $this->Creat_Necessary_Tables();
        $this->Creat_Necessary_Tables_smoov();
        $this->Creat_Necessary_Tables_Recepies_ToSend();

        $this->enqueue_Notification_scripts();
        add_action('admin_notices', array($this, 'show_notice'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_ajax_script'));
        add_action('wp_ajax_admin_enter', array($this, 'handle_admin_enter'));

        //cron jobs==============================================================

        if (is_user_logged_in() && current_user_can('administrator')) {

            if (date('N') == 2) { // 4 for Thursday
                $this->FilterEmailsContainer();
           }
        }
    }

    function encrypt_string($string, $key) {
        $cipher = 'AES-256-CBC';
        $encryption_key = hash('sha256', $key);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($string, $cipher, $encryption_key, 0, $iv);
        
        // Concatenate encrypted data and IV, then encode with Base64
        $encrypted_iv = base64_encode($encrypted . '::' . $iv);
        
        // Remove padding "=" characters
        return rtrim($encrypted_iv, '=');
    }
    
    function decrypt_string($encrypted_string, $key) {
        $cipher = 'AES-256-CBC';
        $encryption_key = hash('sha256', $key);
        
        // Restore "=" padding
        $encrypted_string_padded = $encrypted_string . str_repeat('=', 3 - (strlen($encrypted_string) + 3) % 4);
        
        // Decode the string and split the encrypted data and IV
        list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_string_padded), 2);
        
        return openssl_decrypt($encrypted_data, $cipher, $encryption_key, 0, $iv);
    }
    


    public function SendingNotificationEmailsThruAdmin()
    {
        // Your code here
        $this->SendNotificationsNow();
    }


    public function SendNotificationsNow()
    {

        $name = "notification";
        $value = "sent";
        $expire = time() + (86400 * 30) * 6; // 86400 = 1 day, so this will set the cookie to expire in 30 days
        $path = "/"; // The path on the server where the cookie is available. Use "/" to make it available across the entire domain.

        if (!isset($_COOKIE[$name])) {
            $this->FilterEmailsContainer();
            setcookie($name, $value, $expire, $path);

            // header("Refresh:1");
        }
    }



    function schedule_my_weekly_monday_event()
    {
        if (!wp_next_scheduled('my_weekly_monday_event')) {
            // Schedule the event for next Monday at 8 AM
            $timestamp = strtotime('next monday 8:00');
            wp_schedule_event($timestamp, 'weekly', 'my_weekly_monday_event');
        }
    }


    public function daysDifference($startDate, $endDate)
    {
        // Create DateTime objects for the start date and end date with the format d-m-Y
        $startDateObj = DateTime::createFromFormat('d-m-Y', $startDate);
        $endDateObj = DateTime::createFromFormat('d-m-Y', $endDate);

        // Calculate the difference between the end date and the start date
        $interval = $endDateObj->diff($startDateObj);

        // Return the difference in days
        return $interval->days;
    }



    public function show_notice()
    {
        // Check if the transient is set
        if (get_transient('SendGridReaction')) {
?>
            <div class="notice notice-success is-dismissible">
                <p><?php
                    if (get_transient('SendGridReaction') == 1) {
                        echo 'התראות נשלחו למיילים הרשומים לקטגוריה או ו יוצר זה !';
                    } else {
                        print_r(get_transient('SendGridReaction'));
                    }
                    ?></p>
            </div>
            <?php
            // Delete the transient to prevent notice from showing again
            delete_transient('SendGridReaction');
        }
    }


    private function ErrorHandle($err = [])
    {
        return json_encode($err);
    }


    public function not_icon()
    {
        return get_template_directory_uri() . '/resources/images/message_notification.png';
    }


    public function generateVerificationCode()
    {
        // Get the current date and time including seconds


        $str = 'abcdefghijklmnoAGHIJKLMNOPQRSTUVWXYZ';
        // Generate a random string
        $randomString = str_shuffle($str); // Generates a random 18-character string

        // Combine the formatted date/time and the random string
        $verificationCode =  $randomString;

        return $verificationCode;
    }


    public function generatePassword()
    {
        // Get the current date and time including seconds


        $str = 'abcdezx123456789';
        // Generate a random string
        $randomString = str_shuffle($str); // Generates a random 18-character string

        // Combine the formatted date/time and the random string
        $verificationCode =  $randomString;

        return $verificationCode;
    }




    public function Email_Varefiction_Proccess($email)
    {
        //     


    }

    public function get_Details()
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

            $vareficationCodeToSend = $this->generateVerificationCode();
            $password = $this->generatePassword();
            $data = array(
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'email' => $email,
                'category_id' => $cat_id,
                'recipe_id' => $recipe_id,
                'category_name' => $cat_name,
                'recipe_name' => $recipe_name,
                'valid_user' => !$this->VerefiedEmail($email) ? $vareficationCodeToSend : 'yes',
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'date_of_regist' => date("d-m-Y"),
                'user_subscribe' => $user_subscribe,
                'author_name' => $author_name,
                'author_id' => $author_id,
                'pass_word'=> $password

            );

           
                $result = $wpdb->insert($table_name, $data);

                if ($result === false) {
                    // There was an error with the insert operation
                    print_r($this->ErrorHandle(array("error" => "1", "reaseon" => $wpdb->last_error)));
                }

                if (!$this->VerefiedEmail($email)) {
                $HtmlToSend = $this->SendEmailVerificationToUser($vareficationCodeToSend, $email);
                $this->SendEmailValidation($email,  $HtmlToSend);
                }
                print_r($this->ErrorHandle(array("error" => "0", "reaseon" => $this->group_nots['success_regist'], 'smoov' => $vareficationCodeToSend)));
            } 
           
            
        





        exit;
    }


    public function VerefiedEmail($email)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users';

        $email_verefied = $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE email = %s and valid_user = %s",
            $email,
            'yes'
        );
        $res = $wpdb->get_results($email_verefied);

        if (!empty($res)) {
            return true;
        } else {
            return false;
        }
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
                pass_word VARCHAR(255),
                l1 VARCHAR(255),
                l2 VARCHAR(255),
                l3 VARCHAR(255),

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


    private function Creat_Necessary_Tables_Recepies_ToSend()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_recipes_to_send'; // Your table name

        // Check if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table doesn't exist, create it
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                recipe_id BIGINT(50),
                recipe_name VARCHAR(255),
                main_category_id BIGINT(50),
                main_category_name VARCHAR(50),
                author_id BIGINT(20),
                author_name VARCHAR(50),
                date_of_update VARCHAR(50),
                number_of_emails_dilliverd VARCHAR(255),
                emails_dilliverd LONGTEXT,
               

                PRIMARY KEY  (id)
            ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
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


    public function DrawHTMLbox_notification_Mobile()
    {
        $author_id = get_the_author_meta('ID');

        // Get the author name
        $author_name = get_the_author_meta('display_name');
        $term = $this->get_Primary_Term();

        $rtn = '';
        $rtn .= '<div class="notificationBox">';
        $rtn .= '<div class="h4"><img class="not_icon" src="' . $this->not_icon() . '"/>';
        $rtn .=  $this->group_nots['main_title'] . '</div>';
       // $rtn .= '<div class="secondtitle">' . $this->group_nots['second_title'] . '</div>';
        $rtn .= '<form id="notification_form">
   
    <div class="cat_wrapper">
    <div class="term_add" id="term_add">
    <span  class="add_term_plus" id="add_term" data-id="' . $term['term_id'] . '" data-name="' . $term['term_Name'] . '"></span>
    <span >' . $term['term_Name'] . '</span>
    </div>
    <div class="term_add" id="author_add">
    <span class="add_author" id="add_author" data-id="' . $author_id . '" data-name="' . $author_name . '"></span>
    <span >' . $author_name . '</span>
    </div>
    </div>

    <div class="formWrapper dn">
    <div class="close_frm">X</div>
    <input type="email" name="email" id="email" class="not_email" placeholder="הכניסו מייל..."/>
    
    <div class="agreement_wrap">
    <input type="radio" name="user_subscribe" id="user_subscribe" checked />
    <label for="user_subscribe" id="user_subscribe_label">
    <a href="' . $this->use_agreement_url . '">' . $this->use_agreement_text . '<a/>
    </label>
    </div>    
    <input type="submit" class="submit" value="שלח >>" />  
   </div>
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

//DESKTOP :

public function DrawHTMLbox_notification_Desktop()
{
    $author_id = get_the_author_meta('ID');

        // Get the author name
        $author_name = get_the_author_meta('display_name');
        $term = $this->get_Primary_Term();

        $rtn = '';
        $rtn .= '<div class="notificationBox">';
        $rtn .= '<div class="h4_desktop"><img class="not_icon" src="' . $this->not_icon() . '"/></div>';
        $rtn .= '<div class="m_title">' . $this->group_nots['main_title'] . '</div>';
       // $rtn .= '<div class="secondtitle">' . $this->group_nots['second_title'] . '</div>';
        $rtn .= '<form id="notification_form">
   
    <div class="cat_wrapper">
    <div class="term_add" id="term_add">
    <span  class="add_term_plus" id="add_term" data-id="' . $term['term_id'] . '" data-name="' . $term['term_Name'] . '"></span>
    <span >' . $term['term_Name'] . '</span>
    </div>
    <div class="term_add" id="author_add">
    <span class="add_author" id="add_author" data-id="' . $author_id . '" data-name="' . $author_name . '"></span>
    <span >' . $author_name . '</span>
    </div>
    </div>

    <div class="formWrapper dn">
    <div class="close_frm">X</div>
    <input type="email" name="email" id="email" class="not_email" placeholder="הכניסו מייל..."/>
    
    <input type="submit" class="submit" value="שלח >>" />  
   </div>

    <input type="hidden" name="action" id="action" value="notification_action_call"/>

    <input type="hidden" name="cat_id" id="cat_id" value=""/>
    <input type="hidden" name="cat_name" id="cat_name" value=""/>
    <input type="hidden" name="author_id" id="author_id" value=""/>
    <input type="hidden" name="author_name" id="author_name" value=""/>
    <input type="hidden" name="recipe_id" id="recipe_id" value="' . get_the_ID() . '"/>
    <input type="hidden" name="recipe_name" id="recipe_name" value="' . get_the_title() . '"/>
    <input type="hidden" name="recipe_name" id="recipe_name" value="' . get_the_title() . '"/>
   
   <div class="agreement_wrap dn">
   <input type="radio" name="user_subscribe" id="user_subscribe" checked />
   <label for="user_subscribe" id="user_subscribe_label">
   <a href="' . $this->use_agreement_url . '">' . $this->use_agreement_text . '<a/>
   </label>
   </div>
    </form>';
        $rtn .= '<p id="notification_ajax_response"></p> ';


        return $rtn;
}



//DESKTOP CSS
public function DrawCSS_Notification_Desktop()
{
    $rtn = '
<style>

#notification_ajax_response{
display: block;
    position: absolute;
    width: 69%;
    right: 55px;
    background: #fff;
    color: red;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    font-size:15px;
   
}

.po{
position: absolute;
    top: 14px;
}


.nbox{
display:block !important;
}

.m_title100{
width: 100% !important;
}

#notification_form{
width: 62%;
    display: inline-flex;
    flex-wrap: wrap;
    align-content: space-between;
    justify-content: center;
}
#user_subscribe , #user_subscribe_label{
    display:inline-flex;
    color:#57A0BB;
    }
    #user_subscribe{
 appearance: none;
   -webkit-appearance: none;
    accent-color: #57A0BB !important;
    background-color: #57A0BB;
    border: 4px solid #d1dfe3 !important;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    position: absolute;
    right: -1px;
    top: 4px;
    right: 3px;

    }
 #user_subscribe_label a{
    padding-right: 20px;
    color:#57A0BB !important;
    }


.close_frm{
   position: absolute;
    top: 2px;
    font-size: 20px;
    font-weight: bold;
    z-index: 9999;
    cursor: pointer;
    transition: all 1s ease-out;
    left: 0px;
    z-index: 777;
}
.dn{
display:none;
transition: all 1s ease-out;
}
.formWrapper{
position: relative;
    transition: all 1s ease-in;
    width: 100%;
}

.m_title{
font-size: 14px;
    font-weight: bold;
    margin-right: 7px;
}

.not_icon{  
  width: 27px;
}
.notificationBox .h4{
width:100%;
text-align:center;
color:#333333;
font-size:18px !important;
font-weight:700;
position:relative;
}
.secondtitle{
width:100%;
text-align:center;
color:#333333;
font-size:23px;
}

    .notificationBox{
   position: relative;
    margin: 0 auto;
    text-align: center;
    margin-top: 20px;
    margin-bottom: 20px;
    border-bottom: solid 2px #57A0BB;
    border-top: solid 2px #57A0BB;
    border-radius: 0px;
    padding: 10px;
    background: #fff;
    align-items: center;
    display: flex;
    justify-content: flex-start;
    align-content: space-around;
    flex-wrap: wrap;
    flex-direction: row;
    }
   
    .submit{
  width: 116px;
    background: #57A0BB !important;
    color: #fff !important;
    font-size: 18px !important;
    font-weight: bold;
    padding: 0px !important;
    height: 35px;
    margin-right: -6px;
        
    }
    .add_term_plus , .add_author {
    font-size: 30px;
    font-weight: bold;
    position: absolute;
    top: -1px;
    right: 4px;
    }
    .term_add{
       min-width: 207px;
    position: relative;
    padding-top: 3px;
    border: solid 1px #fff;
    font-size: 16px;
    font-weight: 800;
    display: inline-block;
    cursor: pointer;
    text-align: center;
    background: #f5f5f5;
    color: #57A0BB;
    height: 32px;

    }
   
    .term_add:hover{
        background: #57A0BB;
        color:#fff !important;
        font: size 18px;
        font-weight:800;
    }
.agreement_wrap{
width:89%;
    text-align: right;
    position: relative;
    color: #57A0BB;
    margin-top: 30px;
   
   
}
    .agreement_wrap link{
width: 100%;
    text-align: right;
    position: relative;
    color:#57A0BB;
}
 .not_email{
        
        height:35px;
        text-align:center;
        color: #333333;
        border:solid 0px #fff !important; 
         box-shadow: none !important;
         display: inline-flex;
    width: 71%;
    background:#f5f5f5 !important;

   
    }
.not_valid{

appearance: none;
-webkit-appearance: none;
border:solid 1px red !important;
background:#EFAAA5 !important;
color:#fff;
}
</style>
';
    return $rtn;
}









    public function DrawCSS_Notification_Mobile()
    {
        $rtn = '
    <style>

    #user_subscribe , #user_subscribe_label{
    display:inline-flex;
    color:#57A0BB;
    }
    #user_subscribe{
 appearance: none;
   -webkit-appearance: none;
    accent-color: #57A0BB !important;
    background-color: #57A0BB;
    border: 4px solid #d1dfe3 !important;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    position: absolute;
    right: -1px;
    top: 4px;
    right: 3px;

    }
 #user_subscribe_label a{
    padding-right: 20px;
    color:#57A0BB !important;
    }


.close_frm{
    position: absolute;
    top: -35px;
    font-size: 20px;
    font-weight: bold;
    z-index: 9999;
    cursor: pointer;
    transition: all 1s ease-out;
    left: 1px;
    z-index: 777;
}
.dn{
display:none;
transition: all 1s ease-out;
}
.formWrapper{
position:relative;   
transition: all 1s ease-in;
  
}
.not_icon{  
  width: 27px;
    position: absolute;
    right: -8px;
    top: -6px;
}
.notificationBox .h4{
width:100%;
text-align:center;
color:#333333;
font-size:18px !important;
font-weight:700;
position:relative;
}
.secondtitle{
width:100%;
text-align:center;
color:#333333;
font-size:23px;
}

    .notificationBox{
    position:relative;
    margin: 0 auto;
    text-align: center;
    margin-top: 20px;
    margin-bottom: 20px;
    border-bottom: solid 2px #57A0BB;
    border-top: solid 2px #57A0BB;
    border-radius: 0px;
    padding: 10px;
    background:#fff;
    }
   
    .submit{
  margin-top: 10px;
    width: 162px;
    background: #57A0BB !important;
    color: #fff !important;
    font-size: 18px !important;
    font-weight: bold;
    padding: 0px !important;
    height: 32px;
        
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
        border:solid 1px #fff;
        font: size 18px;
        font-weight:800;
        display: inline-block;
        margin-top:10px;
        cursor: pointer;
        text-align:center;
        background:#f5f5f5;
        color:#57A0BB;

    }
   
    .term_add:hover{
        background: #57A0BB;
        color:#fff !important;
        font: size 18px;
        font-weight:800;
    }
.agreement_wrap{
width: 100%;
    text-align: right;
    position: relative;
    color:#57A0BB;
    margin-top:20px;
}
    .agreement_wrap link{
width: 100%;
    text-align: right;
    position: relative;
    color:#57A0BB;
}
 .not_email{
        width: 100%;
        height:46px;
        text-align:center;
        color: #333333;
        border:solid 0px #fff !important; 
         box-shadow: none !important;
 background:#f5f5f5 !important;
   
    }
.not_valid{

appearance: none;
-webkit-appearance: none;
border:solid 1px red !important;
background:#EFAAA5 !important;
color:#fff;
}
    </style>
    ';
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


    private function GetCurrentRecipe()
    {

        $post = get_post(get_the_ID());
        return $post;
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
            if (empty($this->check_Last_smoovList())) {
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
                        <th>סיסמה</th>
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
                            <td><?php echo $row->pass_word; ?></td>
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



    private function inserLast_db_smoov_list($name, $recipe_id)
    {
        global $wpdb;

        // Replace 'your_table_name' with the actual name of your table
        $table_name = $wpdb->prefix . 'notification_smoov_lists';

        $data_to_insert = array(
            'last_smoov_list' => $name,
            'created_date' => date('d-m-y'),
            'recipe_id' => $recipe_id
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



    private function check_Last_smoovList()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_smoov_lists';
        $field = $this->SmoovListName;
        $Sql = $wpdb->prepare('SELECT * FROM ' . $table_name  . ' WHERE last_smoov_list = %s', $field);
        $result = $wpdb->get_results($Sql);
        return $result;
    }




    private function notification_recipes_to_send_fnc($fields = [])
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_recipes_to_send';
        $result = $wpdb->insert($table_name, $fields);
        return $result;
    }


    private function CheckRecipeInSendigTable($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_recipes_to_send';

        $query  = "SELECT * FROM " . $table_name . "  WHERE recipe_id = " . $id . " and recipe_name <>'Auto Draft'";

        $result = $wpdb->get_results($query);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    public function on_SavingRecipe($post_id, $post, $update)
    {
        $post_date = get_the_date('d-m-Y', $post_id);

        //NEED TO REMOVE COMMENTS BELLOW SO THAT WORK WITH NEW RECIPIES ONLY 
        // AND NOT UPDATED RECIPIES
        //======================================================================
        //    if ($this->daysDifference($post_date , date("d-m-Y")) > 7){
        //     return;
        //    }  
        //=======================================================================
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if ($post->post_status === 'auto-draft') {
            return;
        }
        // Check post type to make sure it's "recipe"
        if ($post->post_type != 'foody_recipe') {
            return;
        }
        if ($this->CheckRecipeInSendigTable($post_id) != 1) {
            $author_id = $post->post_author;
            $author_info = get_userdata($author_id);
            $recipe_name = get_post($post_id);
            $term = ($this->get_Primary_Term());
            $fields = array(
                'recipe_id' => $post_id,
                'recipe_name' => $recipe_name->post_title,
                'main_category_id' => $term['term_id'],
                'main_category_name' =>  $term['term_Name'],
                'author_id' => $author_id,
                'author_name' =>  $author_info->user_nicename,
                'date_of_update' => date('d-m-Y')

            );
            $this->notification_recipes_to_send_fnc($fields);
        }

        //notification_recipes_to_send



    }


    //ajax CRON : ================================================

    //Getting recipies that updated last 7 days =======================
    private function GetNewRecipies()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "notification_recipes_to_send";
        $SqlQuery = "select * from {$table_name}  WHERE TRIM(number_of_emails_dilliverd) is NULL
        and TRIM(emails_dilliverd) is NULL and STR_TO_DATE(date_of_update, '%d-%m-%Y') >= DATE_SUB(CURDATE(), INTERVAL 8 DAY);";
        $Results = $wpdb->get_results($SqlQuery);
        return $Results;
    }

    //Updating wp_notification_recipes_to_send table when sending email 

    public function Update_notification_recipes_to_send_After_Sending($num, $recipe_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "notification_recipes_to_send";
        $SQL_update = "update {$table_name} 
        set number_of_emails_dilliverd = {$num} 
        where recipe_id = {$recipe_id} limit 1";
        $Results = $wpdb->query($SQL_update);
        return $Results;
    }



    //Getting category or and author ids in the results of GetNewRecipies() function ==============

    public function Get_Cats_Auths_IDS()
    {
        $Results = [
            'cats' => [],
            'auth' => [],
            'rid' => [],
        ];
        //prevent duplication=================================================
        $GetNewRecipies = $this->GetNewRecipies();
        foreach ($GetNewRecipies as $key => $Details) {
            if (!in_array($Details->main_category_id, $Results['cats'])) {
                $Results['cats'][] = $Details->main_category_id;
            }
            if (!in_array($Details->author_id, $Results['auth'])) {
                $Results['auth'][] = $Details->author_id;
            }
            if (!in_array($Details->recipe_id, $Results['rid'])) {
                $Results['rid'][] = $Details->recipe_id;
            }
        }

        // Convert arrays to comma-separated strings
        $Res['cats'] = implode(',', $Results['cats']);
        $Res['auth'] = implode(',', $Results['auth']);
        $Res['rid'] = implode(',', $Results['rid']);

        //output should look like : 
        // === Array([cats] => 206,230,324 [auth] => 6,7,31) ==== 
       
        return $Res;
    }



    private function get_author_by_post_id($post_id)
    {
        // Get the author ID for the post
        $author_id = get_post_field('post_author', $post_id);

        if (!$author_id) {
            return null; // Return null if no author is found
        }

        // Get the author's details
        $display_name = get_the_author_meta('display_name', $author_id);
        $author_email = get_the_author_meta('user_email', $author_id);
        $author_url = get_the_author_meta('user_url', $author_id);

        // Create an array of the author's details
        $author_details = [
            'id' => $author_id,
            'display_name' => $display_name,
            'email' => $author_email,
            'url' => $author_url
        ];

        return $author_details;
    }




    private function Email_Template($category, $recipe, $uniqID = null, $cat_ID=null, $email) // email to unsubscribe
    {
        //Sending Goodies :
        //$category = category name========================
       
        foreach ($recipe as $recipe) {

            $post = get_post($recipe->recipe_id);
            // print_r($recipe);die();
            $author = $this->get_author_by_post_id($post->ID);
            $recipeTitle = $post->post_title;
            $featured_image_url = get_the_post_thumbnail_url($post, 'full'); // 'full' can be replaced with any size like 'thumbnail', 'medium', etc.
            $html  = '<!DOCTYPE html><html lang="he"><head><meta charset="UTF-8">';
            $html .= "<title>מתכון חדש מ FOODY</title>";
            $html .= '<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">';
            $html .= "</head";
            $html .= '<body  style="font-family: Open Sans, sans-serif;">';

            $html .= '<div style="position:relative;direction:rtl;max-width:650px;font-family: Open Sans, sans-serif;'; //DIV STARTS
            $html .= 'height:auto;';
            $html .= 'border: solid 0px #ddd;';
            $html .= 'border-radius:0px;';
            $html .= 'text-align:center;';
            $html .= 'margin: 0 auto;margin-bottom:20px;background-color:#f5f5f5';
            $html .= '">'; //DIV ENDS
            $html .= "<img src='{$this->email_Image_Header}' style='width:100%;'/>";
            $html .= '<div id="firstdv" style="width:100%;margin-top:0px;">';
            $html .= '<h3 style="font-size:45px;font-weight:700;color:#E63A2C">מתכון חדש עלה</h3>';
            $html .= '</div>'; //firstdv closer
            $html .= '<img style="width:98%;" src="' .  $featured_image_url  . '"/>';
            $html .= '<h1 style="width:90%;text-align:center;margin:0 auto;margin-top:20px;font-size:35px; color:#57A0BB;margin-bottom:30px;">' . $recipeTitle . '</h1>';
            $html .= '<div  style="width:98%; margin:0 auto; text-align:center; border:solid 3px #57A0BB;padding-bottom: 20px;"> '; //new recipe wrapp
            $html .= '<h3 style="color:#333333;">מתכון חדש בקטגוריה : </h3>';
            $html .= "<div>
        <span style='color:#333333;width: 176px;font-size: 15px;background-color: #fff;padding: 7px;display: inline-block;text-align: center;vertical-align: middle; margin-left:10px;margin-bottom:5px;'> 
        {$author['display_name']}</span>
         <span style='color:#333333;width: 176px;font-size: 15px;background-color: #fff;padding: 7px;display: inline-block;text-align: center;vertical-align: middle; margin-left:10px;margin-bottom:5px;'>{$category}</span> </div>";
            $html .= "</div>";
            $html .= '<div style="justify-content: center;align-items: center; align-items: center;padding:10px;margin:0 auto;margin-top:30px;width:192px;border-radius:26px;background-color:#E5382D;margin-bottom:30px;">
        <a target="_blank" style="color:#fff !important;text-decoration: none;" href="'.$this->EnvyormentType.'/?p=' . $post->ID . '" > לעמוד מתכון >></a></div>  ';
           // $html .= '<span style="padding-bottom:20px;"><a style="color:#3333335c;font-size:14px;text-decoration: none;" href="'.$this->EnvyormentType .'/unsubscribe?unid=' . $uniqID . '&email='.$email.'" >לביטול הרשמה</a></span> |  ';
            $html .= '<div style="padding-bottom:20px;"><a style="color:#3333335c;font-size:14px;text-decoration: none;" href="'.$this->EnvyormentType .'/unsubscribe?cat='.$cat_ID. '-' .$author['id'] .'&unid=' . $uniqID . '&email='.$this->encrypt_string($email, 'bar').'" >להסרה מרשימת התפוצה</a></div> ';
            $html .= '</div>'; //div closer
            $html .= '</body></html>';
        }
        return $html;
    }



    private function SendEmailVerificationToUser($vereficationCode, $email)
    {

        $html  = '<!DOCTYPE html><html lang="he"><head><meta charset="UTF-8">';
        $html .= "<title>מייל אימות - FOODY</title>";
        $html .= '<link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">';
        $html .= "</head";
        $html .= '<body  style="font-family: Open Sans, sans-serif;">';
        $html .= '<div style="position:relative;direction:rtl;max-width:650px;font-family: Open Sans, sans-serif;'; //DIV STARTS
        $html .= 'height:auto;';
        $html .= 'border: solid 0px #ddd;';
        $html .= 'border-radius:0px;';
        $html .= 'text-align:center;';
        $html .= 'margin: 0 auto;margin-bottom:20px;background-color:#f5f5f5';
        $html .= '">'; //DIV ENDS
        $html .= "<img src='{$this->email_Image_Header}' style='width:100%;'/>";
        $html .= '<div id="firstdv" style="width:100%;margin-top:0px;">';
        //$html .= '<h3 style="font-size:45px;font-weight:700;color:#E63A2C">מייל אימות - FOODY</h3>';
        $html .= '<p>ביקשתם להירשם לקבלת התראות</p>';
        $html .= "<a href='{$this->EnvyormentType}/email-verification/?v={$vereficationCode}&e={$email}'>יש ללחוץ כאן כדי לאשר את המייל</a>";
        $html .= '<p>FOODY</p>';
        $html .= '</div>'; //firstdv closer
        return $html;
    }




    //Gets useres (emails) that is registed to the ids of Get_Cats_Auths_IDS() function ============
    private function get_Emails_By_Cat_Auth_ToSend()
    {
        $emailsContainer = [];

        $Get_Cats_Auths_IDS = $this->Get_Cats_Auths_IDS();
       
        if(!empty($Get_Cats_Auths_IDS)){
        global $wpdb;
        $table_name = $wpdb->prefix . "notification_users";
        $sqlQuery = "SELECT 
        email, category_id, author_id, author_name
        FROM {$table_name} where
         category_id IN  ({$Get_Cats_Auths_IDS['cats']}) 
        or author_id IN ({$Get_Cats_Auths_IDS['auth']})  AND (valid_user = 'yes')";
        $Results = $wpdb->get_results($sqlQuery, ARRAY_A);

        foreach ($Results as $result) {
            $email = $result['email'];
            unset($result['email']); // Remove the redundant email entry in the nested array

            if (!isset($emailsContainer[$email])) {
                $emailsContainer[$email] = [$email];
            }

            // Check if the recipe already exists for this email
            $recipeExists = false;

            $emailsContainer[$email][] = $result;
        }


        return $emailsContainer;
    }
    }



    private function SendEmails($email, $category = null, $recipe = null, $author = null, $htmlContent = [])
    {

        $author_id = get_post_field('post_author', $recipe);
        $author_name = get_the_author_meta('display_name', $author_id);
        $subject = ' מתכון חדש עלה ב FOODY ';
        $htmlObject = '';
        foreach ($htmlContent as $html) {
            $htmlObject .= $html;
        }

        $emailData = [
            "personalizations" => [
                [
                    "to" => [
                        ["email" => $email] // Correct format: array of arrays with 'email' key
                    ],
                    // You can add more personalizations for additional recipients here if needed
                ]
            ],
            "from" => [
                "email" => "Foody@foody.co.il"
            ],
            "subject" => $subject,
            "content" => [
                [
                    "type" => "text/html",
                    "value" => $htmlObject,
                ]
            ]
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        // $response = rtrim($response, '1');


        return ($response);
    } // END SendEmails();




    private function SendEmailValidation($email,  $htmlContent)
    {
        //SendEmailVerificationToUser($email, $vereficationCode)

        $subject = 'מייל אימות - FOODY';


        $emailData = [
            "personalizations" => [
                [
                    "to" => [
                        ["email" => $email] // Correct format: array of arrays with 'email' key
                    ],
                    // You can add more personalizations for additional recipients here if needed
                ]
            ],
            "from" => [
                "email" => "Foody@foody.co.il"
            ],
            "subject" => $subject,
            "content" => [
                [
                    "type" => "text/html",
                    "value" => $htmlContent,
                ]
            ]
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        // $response = rtrim($response, '1');


        return ($response);
    } // END SendEmails();





    public function GetRecipiesByCatID($cat_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "notification_recipes_to_send";
        $SqlQuery = "select * from {$table_name}  WHERE TRIM(number_of_emails_dilliverd) is NULL
        and TRIM(emails_dilliverd) is NULL and STR_TO_DATE(date_of_update, '%d-%m-%Y') >= DATE_SUB(CURDATE(), INTERVAL 8 DAY)
        and main_category_id = '{$cat_id}' ";
        $Results = $wpdb->get_results($SqlQuery);

        return $Results;
    }


    //DELETING RECIPIES AFTER SENDIG NOTIFICTIONS TO USERS:


    public function DELETE_Recipe_After_Notificion($rid)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "notification_recipes_to_send";
        $SqlQuery = " delete from {$table_name} where recipe_id IN ({$rid}) limit 15 ";

        $Results = $wpdb->get_results($SqlQuery);
        return $Results;
    }






    public function FilterEmailsContainer()
    {
        $get_Emails_By_Cat_Auth_ToSend = $this->get_Emails_By_Cat_Auth_ToSend();

       
        if (!empty($get_Emails_By_Cat_Auth_ToSend)) {
            //print_r($get_Emails_By_Cat_Auth_ToSend);die('dfc44');

            foreach ($get_Emails_By_Cat_Auth_ToSend as $email => $recipes) {

               


                $htmlObject = []; // Initialize the $htmlObject array here to ensure it is reset for each email

                foreach ($recipes as $key => $val) {
                    if ($key > 0) {

                        $recipe_id_obj = $this->GetRecipiesByCatID($val['category_id']);

                        $recipe_id = $recipe_id_obj;
                        $recipiesToDelete[] =  $recipe_id;
                        $category_name = $recipe_id_obj[0]->main_category_name;

                        // $recipe_name = $val['recipe_name'];
                        $htmlObject[] = $this->Email_Template($category_name, $recipe_id, '33test', $val['category_id'], $email);
                    }
                }
                // print_r($recipe_id_obj);
                //Send the email after building the $htmlObject array
                $res = $this->SendEmails($email, $category_name, $recipe_id, '', $htmlObject);



                // Reset the $htmlObject array for the next email (already done by reinitializing in the outer loop)
            }
        }
        $idsToDelete = [];
        foreach ($recipiesToDelete as $v) {
            foreach ($v as $r) {
                $idsToDelete[$r->recipe_id]  = $r->recipe_id;
            }
        }
        $DeleteTheseFuckers = implode(',', $idsToDelete);
        if($DeleteTheseFuckers ){
        $this->DELETE_Recipe_After_Notificion($DeleteTheseFuckers);
        }
    }





    public function enqueue_admin_ajax_script()
    {
        // Register and enqueue your custom JavaScript file
        wp_register_script('admin-ajax-script', get_template_directory_uri() . '/resources/js/notification.js', array('jquery'), null, true);

        // Localize the script with the AJAX URL
        wp_localize_script('admin-ajax-script', 'adminAjax', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));

        wp_enqueue_script('admin-ajax-script');
    }
    //$this->FilterEmailsContainer(); thats what sends the emails
    public function handle_admin_enter()
    {

        if (is_user_logged_in() && current_user_can('administrator')) {
            $current_user = wp_get_current_user();
            $user_name = $current_user->user_login;
            error_log('Admin area accessed by: ' . $user_name);
            wp_send_json_success($this->GetNewRecipies());
        }
        wp_die(); // Always include this to terminate execution
    }
} //end class

//TODO :
//BUILD THE HTML SENDING WITH DATAGRID + UNSUBSCRIBE 


?>