jQuery(".close_banner").on('click', function(e){
IDToClose = (jQuery(this).attr('data-close'));
jQuery("#" + IDToClose ).toggle();
});

jQuery(".banner_wrapper").on("click", function(){
linkURI = (jQuery(this).attr('data-url'));
window.location = linkURI  ; 
});