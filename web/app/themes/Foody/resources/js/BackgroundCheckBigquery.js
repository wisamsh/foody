jQuery(document).ready(function(jQuery) {
    
        jQuery.ajax({
            url: ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'run_background_check'
            },
            success: function(response) {
               console.log(response);
                jQuery('.cron_notice').text(response.data.progress);
            }
        });
});
