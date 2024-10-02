jQuery( document ).ready(function() {
   
    jQuery(".mainhamburger").on('click', function(){
        jQuery(".menu_container").slideToggle(400, "linear");
        jQuery(".mainhamburger").css("display", "none");
        jQuery(".manucloser").css("display", "block");
       
    });
//manucloser
jQuery(".manucloser").on('click', function(){
    jQuery(".manucloser").css("display", "none");
    jQuery(".mainhamburger").css("display", "block");
    jQuery(".menu_container").slideToggle(50);
    
});


});