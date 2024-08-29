jQuery(document).ready(function () {
    
    const queryString = window.location.search;

    // Parse the query string using URLSearchParams
    const urlParams = new URLSearchParams(queryString);
    
    // Get the values of 'w' and 'r'
    const email = urlParams.get('email');
    
    const cat = urlParams.get('cat');



    jQuery('#terminate_all').on('click', function(e) {
        e.preventDefault();

        jQuery.ajax({
            url: myAjax.ajaxUrl,
            type: 'POST',
            data: {
                action: 'unsubscribe',
                nonce: myAjax.nonce,
                email : email,
                cat : cat
            },
            success: function(response) {
                if(!response.error) {
                    //console.log(response.message);
                    jQuery("#response").html(response.data.message)
                    jQuery("#terminate_all").attr('disabled', 'disabled');
                } else {
                   // console.log(response.message);
                   jQuery("#response").html(response.data.message)
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
            email: email,
            cat : cat
        },
        success: function(response) {
            console.log(response)
            if(!response.error) {
                //console.log(response.message);
                jQuery("#response").html(response.data.message)
                jQuery("#category_btn").attr('disabled', 'disabled');
            } else {
               // console.log(response.message);
               jQuery("#response").html(response.data.message)
               jQuery("#category_btn").attr('disabled', 'disabled');
            }
        },
        error: function() {
            jQuery("#response").html('ישנה בעיה להתקשר עם השרת אנא נסו מאוחר יותר!');
        }
    });
});





    



});
