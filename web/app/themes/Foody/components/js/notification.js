jQuery(document).ready(function () {

    //Validations : 
    let term_id = jQuery("#add_term").attr("data-id");
    let term_name = jQuery("#add_term").attr("data-name");

    let author_id = jQuery("#add_author").attr("data-id");
    let author_name = jQuery("#add_author").attr("data-name");


    jQuery("#term_add").on("click", function () {
      
            if(jQuery("#cat_id").val() === ""){
               jQuery("#cat_id").val(term_id);
               jQuery("#cat_name").val(term_name);
               jQuery("#add_term").html("-");
               jQuery("#term_add").addClass("term_add_picked");

            }
            else{
               jQuery("#cat_id").val('');
               jQuery("#cat_name").val('');
               jQuery("#add_term").html("+");
               jQuery("#term_add").removeClass("term_add_picked");
            }
    });


    jQuery("#author_add").on("click", function () {
      
        if(jQuery("#author_id").val() === ""){
           jQuery("#author_id").val(author_id);
           jQuery("#author_name").val(author_name);
           jQuery("#add_author").html("-");
           jQuery("#author_add").addClass("term_add_picked");

        }
        else{
           jQuery("#author_id").val('');
           jQuery("#author_name").val('');
           jQuery("#add_author").html("+");
           jQuery("#author_add").removeClass("term_add_picked");
        }
});








    jQuery('#notification_form').submit(function (e) {
        e.preventDefault(); // Prevent form submission
        var formData = jQuery(this).serialize(); // Sericonsole.log ("fffff",formData['email']);

        jQuery('#notification_ajax_response').html('מעדכן...');

        // AJAX request
        jQuery.ajax({
            type: 'POST',
            url: ajax_object.ajax_url, // URL to admin-ajax.php
            data: formData, // Add action parameter
            success: function (response) {
                var responseData = JSON.parse(response);
                jQuery('#notification_ajax_response').html((responseData.reaseon)); // Display response
            }
        });
    });

});
//notification_form