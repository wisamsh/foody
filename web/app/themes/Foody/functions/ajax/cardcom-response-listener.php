<?php
require_once '../../../../../../vendor/autoload.php';


define('WP_USE_THEMES', true);
$_SERVER['REQUEST_URI'] = '/';
//$_SERVER['HTTP_HOST'] = $argv[1];
require_once '../../../../../../web/wp/wp-load.php';
//require_once '../web/wp/wp-load.php';

if(is_array($_GET) && isset($_GET['lowprofilecode'])){
    check_cardcom_purchase($_GET['lowprofilecode']);
}