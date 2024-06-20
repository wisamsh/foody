jQuery(document).ready(function(jQuery) {
    let loader = '<img src="https://foody-media.s3.eu-west-1.amazonaws.com/w_images/loader22.gif"';
    let HelpLink = '<p>For Any Problom With GoogleBigQuery Please <a href="/wp/wp-admin/admin.php?page=google-bigquery-menu#showbigquery">Press Here For Manual Update</a></p>';
    jQuery('.cron_notice').html('<p>Updating GoogleBigQuery</p><br><p><center>'+loader+'</center></p>' + HelpLink);
    
        jQuery.ajax({
            url: ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'run_background_check'
            },
            success: function(response) {
               
               console.log(response);
               let updt = response.data.updating ;
               let title = '<h3><b>Google Big Query</b></h3>';
               
               let date_quering = '<p><span>last Update : ' + response.data.last_update.date_quering  + '</span></p>';
               let username = '<p><span>By User : ' + response.data.last_update.username  + '</span></p>';
               //let updating = response.data.updating;
               
               jQuery('.cron_notice').html(title + date_quering + username + updt + HelpLink);
               
               //jQuery('.cron_notice').html(response);
            }
        });
});
