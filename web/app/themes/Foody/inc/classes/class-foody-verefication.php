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


}//END CLASS
    ?>