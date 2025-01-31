jQuery(document).ready(function($) {
    jQuery('#update_notificationTable').on('click', function() {
        let post_id = jQuery('#update_notificationTable').attr("data-postid");
        let NotificationToRecipe_meta_box = "NotificationToRecipe_meta_box" + post_id ;

    let message = "<center><b> <p style='color:red;' class='notice notice-info'>המתכון נמחק מטבלת התראות</p></b></center>";

        //alert('works');
        // Perform the AJAX request
        jQuery.ajax({
            type: 'POST',
            url: notification_table_nonce.ajax_url,
            data: {
                action: 'handle_notification_table_update', // Action hook
                nonce: notification_table_nonce.nonce, // Security nonce
                postID : post_id
            },
            success: function(response) {
               // alert('Success: ' + response.data.message);
              // console.log('post : ' , post_id);
               //jQuery("#" + NotificationToRecipe_meta_box).css("display", "none");
               if(response.data.message == 'נמחק'){
                message = message ; 
               }
               else{
                message = response.data.message ;
               }
               jQuery("#" + NotificationToRecipe_meta_box).html(message);
               console.log(response);
            },
            error: function(xhr, status, error) {
               // alert('Error: ' + xhr.responseText);
            }
        });
    });

jQuery("#info_on_not").on('click' , function (){
jQuery("#notification_info").slideToggle();
});



});//end ready
