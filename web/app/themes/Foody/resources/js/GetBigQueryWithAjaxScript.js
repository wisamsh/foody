jQuery(document).ready(function(jQuery) {
    jQuery('#showbigquery').on('click', function() {
        let loader = 'https://foody-media.s3.eu-west-1.amazonaws.com/w_images/loader22.gif';
        jQuery('.result_bigquery').html('<img src="'+loader+'" /><br/>....מעדכן נתונים מגוגל סובלנות בבקשה זה יקח טיפה זמן');
        jQuery.ajax({
            type: 'POST',
            url: BigQueryObject.ajax_url,
            data: {
                action: 'get_big_query_data',
                nonce: BigQueryObject.nonce
            },
            success: function(response) {
                if (response) {
                    jQuery('.result_bigquery').html(JSON.stringify(response));
                } else {
                    jQuery('.result_bigquery').html('No data received.');
                }
            },
            error: function() {
                jQuery('.result_bigquery').html('There was an error processing the request.');
            },
            timeout: 120000
        });
    });
});
