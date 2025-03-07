
<?php
/*
* Template Name: Send Grid Notifiction 
*/

get_header();

$Foody_Notification = new Foody_Notification ;
?>
<?php if ( has_post_thumbnail() ) { ?>
    <div class="featured-image">
        <?php the_post_thumbnail(); ?>
    </div>
<?php } ?>
<?php 
if(!wp_is_mobile()){echo $Foody_Notification->DrawCSS_Notification_Desktop();}
if(wp_is_mobile()){echo $Foody_Notification->DrawCSS_Notification_Mobile();}
echo $Foody_Notification->DrawHTMLbox_notification_all();
echo $Foody_Notification->PopUpModel();
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
</div>
</div>
</div>
<?php get_footer();?>
