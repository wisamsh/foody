<?php 
/**
 * Template Name: Email Verefication
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$Foody_Verfication = new Foody_Verfication ;
$e = ($_GET['e']);
$v = ($_GET['v']);
$res = $Foody_Verfication->CheckVerefictionCode($e, $v);
print_r($res);
?>




<?php 
get_footer();
?>