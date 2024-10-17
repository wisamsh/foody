jQuery( document ).ready(function() {
   
    jQuery(".mainhamburger").on('click', function(){
        jQuery(".menu_container").slideToggle();
        jQuery(".mainhamburger").removeClass('db');
        jQuery(".mainhamburger").addClass('dn');
        jQuery(".manucloser").removeClass('dn');
        jQuery(".manucloser").addClass('db');
       
    });
//manucloser
jQuery(".manucloser").on('click', function(){
   
    jQuery(".menu_container").slideToggle(50);
    jQuery(".mainhamburger").removeClass('dn');
        jQuery(".mainhamburger").addClass('db');
        jQuery(".manucloser").removeClass('db');
        jQuery(".manucloser").addClass('dn');
    


});


});