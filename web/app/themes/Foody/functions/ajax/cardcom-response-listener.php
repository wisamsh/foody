<?php
require_once '../../../../../../vendor/autoload.php';


define('WP_USE_THEMES', true);
$_SERVER['REQUEST_URI'] = '/';
//$_SERVER['HTTP_HOST'] = $argv[1];
require_once '../../../../../../web/wp/wp-load.php';
//require_once '../web/wp/wp-load.php';

if(is_array($_GET) && isset($_GET['lowprofilecode']) && isset($_GET['OperationResponse'])){
    $updated = check_cardcom_purchase_from_notifier($_GET);
    if($updated) {
        $return_code = http_response_code(200); // this will get previous response code and set a new one to 200
        $return_code = http_response_code();
        return $return_code;
    }
}