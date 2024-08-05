<?php
 if (!defined('ABSPATH')) exit;

class Foody_Verfication
{

public function CheckVerefictionCode($email ,$code){
global $wpdb ;
$TableName = $wpdb->prefix . 'notification_users';
$SQL =  $wpdb->prepare("SELECT * FROM {$TableName} where email = %s and valid_user = %s", $email, $code);
$results = $wpdb->get_results($SQL);
return $results ;
}

public function UpdateAndValidUser($email){
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






}//END CLASS
    ?>