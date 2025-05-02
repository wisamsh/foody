jQuery(document).ready(function() {
    jQuery(".close_banner").on('click', function(e){
        e.stopPropagation(); // Prevent bubbling
        var IDToClose = jQuery(this).attr('data-close');
        var SpecialID = jQuery(this).attr('data-id');
        jQuery("#" + IDToClose).toggle();
        setCookie("FB_"+SpecialID, SpecialID, 24);
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

function setCookie(name, value, hours) {
    const d = new Date();
    d.setTime(d.getTime() + (hours * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = `${name}=${value}; ${expires}; path=/`;
  }