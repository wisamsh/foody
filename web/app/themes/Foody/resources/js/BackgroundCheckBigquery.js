jQuery(document).ready(function(jQuery) {
    let loader = '<img src="https://foody-media.s3.eu-west-1.amazonaws.com/w_images/loader22.gif"';
    jQuery('.cron_notice').html('<p>מתעדכן...</p><p><center>'+loader+'</center></p>');
        jQuery.ajax({
            url: ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'run_background_check'
            },
            success: function(response) {
               console.log(response);
               let title = '<h3><b><center>עדכון אחרון מ-Google Big Query למתכונים פופולריים</center></b></h3>';
               
               let date_quering = '<p><span>last Update : ' + response.data.last_update.date_quering  + '</span></p>';
               let username = '<p><span>By User : ' + response.data.last_update.username  + '</span></p>';
               
               jQuery('.cron_notice').html(title + date_quering + username);
            }
        });
});
