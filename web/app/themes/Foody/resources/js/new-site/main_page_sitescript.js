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
    window.addEventListener('scroll', function() {
        
        jQuery(".menu_container").slideUp(0);
        jQuery(".mainhamburger").removeClass('dn');
        jQuery(".mainhamburger").addClass('db');
        jQuery(".manucloser").removeClass('db');
        jQuery(".manucloser").addClass('dn');
        
        
        // You can add additional actions based on scroll position here
      });

    //when scrolling body:
    

    

    jQuery("#searchzoom").on("click", function (event) {
        
        if (jQuery('#searchtext').hasClass('dn')) {
            jQuery("#closesearchbox").removeClass('dn');
            jQuery("#searchtext").removeClass("dn");
             jQuery('#searchtext').animate({
                 width: '380px' // Adjust the width to your desired value
             }, 100);
             event.stopPropagation();
        }
        
    });

    jQuery("#closesearchbox").on("click", function (event) {
       
        jQuery('#searchtext').animate({
            width: '0px' // Adjust the width to your desired value
        }, 100);
        jQuery('#searchtext').addClass('dn');
        jQuery("#closesearchbox").addClass('dn');
        
    })
    
jQuery("#searchzoommbl").on("click", function(){
    if(jQuery(".searchWrapper").hasClass("dn")){
jQuery(".searchWrapper").removeClass("dn");
    }
    else{
        jQuery(".searchWrapper").addClass("dn"); 
    }

});

//sharing : 
//share_open
jQuery("#share_open").on("click", function(){
if(jQuery(".shareIconsWrapper").hasClass("dn")){


jQuery(".shareIconsWrapper").animate({
                 width: '194px' // Adjust the width to your desired value
             }, 100);
}
jQuery(".shareIconsWrapper").removeClass("dn");
});

jQuery("#close_share_dsktp").on("click", function(){
    
jQuery(".shareIconsWrapper").animate({
    width: '0px' // Adjust the width to your desired value
}, 100);
    jQuery(".shareIconsWrapper").addClass("dn");
});

//mobile : 

jQuery("#share_ocm").on("click", function(){
    
    jQuery(".shareIconsWrapper").animate({
        height: 'auto' // Adjust the width to your desired value
    }, 100);
        jQuery(".shareIconsWrapper").removeClass("dn");
    });



jQuery("#close_share_mbl").on("click", function(){
   
    
        jQuery(".shareIconsWrapper").addClass("dn");

});


});

