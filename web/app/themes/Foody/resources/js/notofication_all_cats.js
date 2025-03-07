jQuery(document).ready(function($) {
    
    
    jQuery("#notification_form_all").submit(function (event) {
        event.preventDefault();

       
       
        if(jQuery("#email").val() == ''){
            alert ('יש להזין אימייל!');
            jQuery("#notification_ajax_response_all").addClass("dn");
            return;
        }
        jQuery("#notification_ajax_response_all").removeClass("dn");
        jQuery("#notification_ajax_response_all").html('טוען...');

         // Prevent default form submission

        var formData = jQuery(this).serialize(); // Serialize form data

        jQuery.ajax({
            url: allnots_ajax_object.ajax_url, // WP AJAX URL
            type: "POST",
            data: {
                action: "sfalntf", // WordPress action hook
                formData: formData, // Serialized form data
            },
            success: function (response) {
                jQuery("#notification_ajax_response_all").removeClass("dn");
                jQuery("#notification_ajax_response_all").html(response); // Show response message
            },
            error: function () {
                jQuery("#notification_ajax_response_all").removeClass("dn");
                jQuery("#notification_ajax_response_all").html("<p style='color:red;'>Error sending data</p>");
            },
        });
    });

});
