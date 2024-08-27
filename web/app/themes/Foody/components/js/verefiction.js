jQuery(document).ready(function () {
    
    jQuery('#terminate_all').on('click', function(e) {
        e.preventDefault();

        jQuery.ajax({
            url: myAjax.ajaxUrl,
            type: 'POST',
            data: {
                action: 'unsubscribe',
                nonce: myAjax.nonce
            },
            success: function(response) {
                if(response) {
                    console.log(response.res);
                } else {
                    alert('Something went wrong');
                }
            },
            error: function() {
                alert('AJAX request failed');
            }
        });
    });

//Category==================================================

jQuery('#category_btn').on('click', function(e) {
    e.preventDefault();

    jQuery.ajax({
        url: myAjax.ajaxUrl,
        type: 'POST',
        data: {
            action: 'unsubscribecat',
            nonce: myAjax.nonce
        },
        success: function(response) {
            if(response) {
                console.log(response.res);
            } else {
                alert('Something went wrong');
            }
        },
        error: function() {
            alert('AJAX request failed');
        }
    });
});





    



});
