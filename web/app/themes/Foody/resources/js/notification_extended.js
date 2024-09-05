jQuery(document).ready(function($) {
    jQuery('#update_notificationTable').on('click', function() {
        //alert('works');
        // Perform the AJAX request
        jQuery.ajax({
            type: 'POST',
            url: notification_table_nonce.ajax_url,
            data: {
                action: 'handle_notification_table_update', // Action hook
                nonce: notification_table_nonce.nonce, // Security nonce
            },
            success: function(response) {
               // alert('Success: ' + response.data.message);
            },
            error: function(xhr, status, error) {
               // alert('Error: ' + xhr.responseText);
            }
        });
    });
});
