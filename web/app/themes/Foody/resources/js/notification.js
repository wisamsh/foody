jQuery(document).ready(function($) {
    // AJAX call when entering admin area
    jQuery.ajax({
        url: adminAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'admin_enter'
        },
        success: function(response) {
            console.log('Success:', response);
        },
        error: function(xhr, status, error) {
            console.log('Error:', error);
        }
    });
});
