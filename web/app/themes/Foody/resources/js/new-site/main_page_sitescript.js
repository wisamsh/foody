jQuery(document).ready(function () {
    
    jQuery(".mainhamburger").on('click', function () {
        jQuery(".menu_container").slideToggle();
        jQuery(".mainhamburger").removeClass('db');
        jQuery(".mainhamburger").addClass('dn');
        jQuery(".manucloser").removeClass('dn');
        jQuery(".manucloser").addClass('db');

    });
    //manucloser
    jQuery(".manucloser").on('click', function () {

        jQuery(".menu_container").slideToggle(50);
        jQuery(".mainhamburger").removeClass('dn');
        jQuery(".mainhamburger").addClass('db');
        jQuery(".manucloser").removeClass('db');
        jQuery(".manucloser").addClass('dn');



    });

    

    jQuery("#searchzoom").on("click", function (event) {
        
        if (jQuery('#searchtext').hasClass('dn')) {
            jQuery("#closesearchbox").removeClass('dn');
            jQuery("#searchtext").removeClass("dn");
             jQuery('#searchtext').animate({
                 width: '380px' // Adjust the width to your desired value
             }, 500);
             event.stopPropagation();
        }
    });

    jQuery("#closesearchbox").on("click", function (event) {
       
        jQuery('#searchtext').animate({
            width: '0px' // Adjust the width to your desired value
        }, 1000);
        jQuery('#searchtext').addClass('dn');
        jQuery("#closesearchbox").addClass('dn');
    })
    


});