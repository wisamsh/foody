<?php
if (!defined('ABSPATH')) exit;

class Foody_Verfication
{
    private $EncriptionKey;
    public $getEncryptEmail;
    public function __construct()
    {
        $this->EncriptionKey = 'bar';
        $this->getEncryptEmail = isset($_GET['email']) ? $_GET['email'] : null;
        add_action('init', array($this, 'WPactions'));
        // Hook into the admin and public AJAX actions
        $this->enqueue_my_ajax_script();
    }


    function encrypt_string($string, $key) {
        $cipher = 'AES-256-CBC';
        $encryption_key = hash('sha256', $key);
        $iv = str_repeat('0', openssl_cipher_iv_length($cipher)); // Fixed IV with zeros
        $encrypted = openssl_encrypt($string, $cipher, $encryption_key, 0, $iv);
        
        // Encode encrypted data with Base64
        $encrypted_iv = base64_encode($encrypted);
        
        return $encrypted_iv;
    }
    
    function decrypt_string($encrypted_string, $key) {
        $cipher = 'AES-256-CBC';
        $encryption_key = hash('sha256', $key);
        $iv = str_repeat('0', openssl_cipher_iv_length($cipher)); // Fixed IV with zeros
        
        // Decode from Base64
        $encrypted_data = base64_decode($encrypted_string);
        
        // Decrypt the data
        $decrypted = openssl_decrypt($encrypted_data, $cipher, $encryption_key, 0, $iv);
        
        return $decrypted;
    }
    



    public function enqueue_my_ajax_script()
    {
        wp_enqueue_script('unsubsucriberScript', get_template_directory_uri() . '/components/js/verefiction.js', array('jquery'), null, true);

        // Localize the script with necessary data
        wp_localize_script('unsubsucriberScript', 'myAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('my_ajax_nonce'),

        ));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_my_ajax_script'));
    }

    public function WPactions()
    {

        add_action('wp_ajax_unsubscribe', array($this, 'unsubscribe_ajax_request'));
        add_action('wp_ajax_nopriv_unsubscribe', array($this, 'unsubscribe_ajax_request'));

        add_action('wp_ajax_unsubscribecat', array($this, 'unsubscribecat_ajax_request'));
        add_action('wp_ajax_nopriv_unsubscribecat', array($this, 'unsubscribecat_ajax_request'));
    }

    public function CheckingEmailifExist($email)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users';

        // Prepare and execute the SQL statement to delete the record
        $search = $wpdb->query(
            $wpdb->prepare(
                "SELECT id, email FROM {$table_name} WHERE email = %s ",
                $email
            )
        );
        return  $search;
    }


    public function CheckVerefictionCode($email, $code)
    {
        global $wpdb;
        $TableName = $wpdb->prefix . 'notification_users';
        $SQL =  $wpdb->prepare("SELECT * FROM {$TableName} where email = %s and valid_user = %s", $email, $code);
        $results = $wpdb->get_results($SQL);
        return $results;
    }

    public function UpdateAndValidUser($email)
    {
        global $wpdb;
        $TableName = $wpdb->prefix . 'notification_users';
        $data = array(
            'valid_user' =>  'yes'
        );

        $where = array(
            'email' => $email
        );

        $result = $wpdb->update($TableName, $data, $where);
        return $result;
    }


    public function BindLogo()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');

        if (has_custom_logo()) {
            return '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '">';
        } else {
            return '<h1>' . get_bloginfo('name') . '</h1>';
        }
    }
    //UNSUBSCRIBE:=================================================================================

    public function unsubscribe_ajax_request()
    {
        //EncriptionKey
        $encryptedEmail = isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
        $email = $this->decrypt_string($encryptedEmail, $this->EncriptionKey);

        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users';

        // Prepare and execute the SQL statement to delete the record
        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table_name} WHERE email = %s LIMIT 20",
                $email
            )
        );

        if ($deleted) {
            wp_send_json_success(array('error' => 0, 'message' => 'המחיקה התבצעה בהצלחה', 'del' => $deleted));
        } else {
            wp_send_json_error(array('error' => 1, 'message' => 'מייל זה אינו רשום במערכת!', 'del' => $deleted));
        }

        // Send a successful JSON response

        wp_die();
    }




    public function unsubscribecat_ajax_request()
    {
        $encryptedEmail = isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
        $email = $this->decrypt_string($encryptedEmail, $this->EncriptionKey);

       if (isset($_REQUEST['cat']) && $_REQUEST['cat'] != '') {
            $cat_arr = explode("-", $_REQUEST['cat']);
            $cat_id = $cat_arr[0];
            $author_id = $cat_arr[1];
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'notification_users';

        $delete_category =  "DELETE FROM {$table_name} WHERE email = %s and category_id = %s limit 20 ";
        $delete_author =  "DELETE FROM {$table_name} WHERE email = %s and author_id = %s";
        
        $delete_reference = $cat_id == 0 ? $author_id : $cat_id ; 
        $delete_query = $cat_id == 0 ? $delete_author : $delete_category;


        $deleted = $wpdb->query(
            $wpdb->prepare(
                $delete_query,
                $email,
                $delete_reference 

            )
        );
        $error = $wpdb->last_error;

        if ($deleted) {
            wp_send_json_success(array('error' => 0, 'message' => 'המחיקה התבצעה בהצלחה', 'del' => $deleted));
        } else {
            wp_send_json_error(array('error' => 1, 'message' => 'אתם כבר אינכם רשומים ליוצר או קטגוריה!', 'del' => $deleted));
        }

        // Send a successful JSON response

        wp_die();
    }
} //END CLASS
