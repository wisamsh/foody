jQuery(document).ready(function($) {
    

    jQuery("#notification_form_all").submit(function (event) {
        event.preventDefault(); // Prevent default form submission

        var formData = jQuery(this).serialize(); // Serialize form data
console.log(formData);
        // $.ajax({
        //     url: my_ajax_object.ajax_url, // WP AJAX URL
        //     type: "POST",
        //     data: {
        //         action: "submit_form", // WordPress action hook
        //         formData: formData, // Serialized form data
        //     },
        //     success: function (response) {
        //         $("#response").html(response); // Show response message
        //     },
        //     error: function () {
        //         $("#response").html("<p style='color:red;'>Error sending data</p>");
        //     },
        // });
    });

});
