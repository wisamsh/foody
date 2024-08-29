jQuery(document).ready(function () {
    
    const queryString = window.location.search;

    // Parse the query string using URLSearchParams
    const urlParams = new URLSearchParams(queryString);
    
    // Get the values of 'w' and 'r'
    const email = urlParams.get('email');
    




    jQuery('#terminate_all').on('click', function(e) {
        e.preventDefault();

        jQuery.ajax({
            url: myAjax.ajaxUrl,
            type: 'POST',
            data: {
                action: 'unsubscribe',
                nonce: myAjax.nonce,
                email : email
            },
            success: function(response) {
                if(!response.error) {
                    //console.log(response.message);
                    jQuery("#response").html(response)
                } else {
                   // console.log(response.message);
                   jQuery("#response").html(response)
                }
            },
            error: function() {
                jQuery("#response").html('ישנה בעיה להתקשר עם השרת אנא נסו מאוחר יותר!');
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
            nonce: myAjax.nonce,
            email: email
        },
        success: function(response) {
            if(!response.error) {
                //console.log(response.message);
                jQuery("#response").html(response)
            } else {
               // console.log(response.message);
               jQuery("#response").html(response)
            }
        },
        error: function() {
            jQuery("#response").html('ישנה בעיה להתקשר עם השרת אנא נסו מאוחר יותר!');
        }
    });
});





    



});
