jQuery( document ).ready(function() {
    jQuery('#notification_form').submit(function(e) {
        e.preventDefault(); // Prevent form submission
        var formData = jQuery(this).serialize(); // Serialize form data
        jQuery('#notification_ajax_response').html('מעדכן...');

        // AJAX request
        jQuery.ajax({
            type: 'POST',
            url:ajax_object.ajax_url, // URL to admin-ajax.php
            data: formData, // Add action parameter
            success: function(response) {
                var responseData = JSON.parse(response);
                jQuery('#notification_ajax_response').html((responseData.reaseon)); // Display response
            }
        });
    });
});
//notification_form