jQuery(document).ready(function() {
    jQuery(".close_banner").on('click', function(e){
        e.stopPropagation(); // Prevent bubbling
        var IDToClose = jQuery(this).attr('data-close');
        jQuery("#" + IDToClose).toggle();
    });

    jQuery(".banner_wrapper_2").on("click", function(){
        // Prevent redirect if the close button (or child) was clicked
        // if (jQuery(e.target).closest('.close_banner').length) {
        //     return; // Do nothing
        // }

        var linkURI = jQuery(this).attr('data-url');
        window.location = linkURI;
    });
});
