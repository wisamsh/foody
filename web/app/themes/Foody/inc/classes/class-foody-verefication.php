<?php
if (!defined('ABSPATH')) exit;

class Foody_Verfication
{

    public function __construct()
    { 
        add_action('init', array($this, 'WPactions'));
        // Hook into the admin and public AJAX actions
        $this->enqueue_my_ajax_script();
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
    


    public function enqueue_my_ajax_script()
    {
        wp_enqueue_script('unsubsucriberScript', get_template_directory_uri() . '/components/js/verefiction.js', array('jquery'), null, true);

        // Localize the script with necessary data
        wp_localize_script('unsubsucriberScript', 'myAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('my_ajax_nonce')
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



    public function unsubscribe_ajax_request()
    {
        $data = array(
            
            'res'  => 'unsubscribe all ',
            'email' => 'All',
        );
    
        // Send a successful JSON response
        wp_send_json_success($data);
        wp_die(); 
    }
public function unsubscribecat_ajax_request(){
    $data = array(
        'res'  => 'unsubscribe Category ',
        'email' => 'Category',
    );

    // Send a successful JSON response
    wp_send_json_success($data);
    wp_die(); 
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


    //UNSUBSCRIBE:=================================================================================

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
} //END CLASS
