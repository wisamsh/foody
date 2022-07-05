jQuery("#poll").on("submit", (function(e)

{
    let data = jQuery("#poll").serialize();
e.preventDefault();



jQuery.ajax({
    type : "POST",
    url :"/wp/wp-admin/admin-ajax.php",
  
    data : {
    "action": "Poll_Ajax_Call",
    "data": data
    
    
    },
    
    success: function(response, status, jqXHR) {
    
    
    }
});




}));