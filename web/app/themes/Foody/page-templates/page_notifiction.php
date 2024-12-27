<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<?php
/*
* Template Name: Send Grid Notifiction 
*/

get_header();
$Foody_Notification = new Foody_Notification ;
?>
<?php 
if(!wp_is_mobile()){echo $Foody_Notification->DrawCSS_Notification_Desktop();}
if(wp_is_mobile()){echo $Foody_Notification->DrawCSS_Notification_Mobile();}
echo $Foody_Notification->DrawHTMLbox_notification_all();
?>
<?php //global style:?>
<style>
    .form-check-label{
    background: #57a0bb;
    width: 100%;
    padding: 10px;
    color: #fff;
    border-radius: 5px;
    margin-bottom: 4px;
    cursor: pointer;
}
.form-check-input{
    position: absolute;
    right: 19px;
    cursor: pointer;
    top: 5px;
    accent-color: #57a0bb;
}
.cat_wrapps{
        width: 100%;
    display: contents;
    }
</style> 
<?php //css adjustments :

if(!wp_is_mobile()){
?>
<style>
   
   
    .notificationBox {
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
    align-items: baseline !important;
    display: flex;
    justify-content: center !important;
    align-content: space-around !important;
    flex-wrap: wrap!important;
    flex-direction: row!important ;
}
.text_wrapp{
    width: 100%;
    padding: 20px;
    margin: 20px;
}
.agreement_wrap{
    width: 85% !important;
    text-align: right !important;
    position: relative !important;
    color: #57A0BB;
    margin: 0 auto !important;
    margin-top: 10px !important;
    }
</style>
<?php }?>

<?php //css adjustments :
if(wp_is_mobile()){
?>
<style>
    .not_icon {
    width: 27px;
    position: absolute;
    right: 13px !important;
    top: 44px !important;
    z-index: 888!important;
    }
   
</style>
<?php }?>
<?php get_footer();?>
