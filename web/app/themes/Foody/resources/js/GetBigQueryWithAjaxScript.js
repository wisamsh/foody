jQuery(document).ready(function(jQuery) {
    jQuery('#showbigquery').on('click', function() {
        jQuery('.result_bigquery').html('מעדכן מתכונים מגוגל...');
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
