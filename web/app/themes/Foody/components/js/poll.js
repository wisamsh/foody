
jQuery("#back_to_poll").click(function () {
    jQuery(".form_wrapper").slideToggle(500);
    jQuery("#back_to_poll").addClass("dn");

});



jQuery("#poll").on("submit", (function (e) {

    jQuery("#poll input[type=radio]")
        .each(function () {


            console.log(this.checked);
        });



    let data = jQuery("#poll").serialize();
    jQuery(".menus_for_you").html("");



    e.preventDefault();
    jQuery("#poll_calc_btn").removeClass("poll_calc_btn");
    jQuery("#poll_calc_btn").addClass("poll_calc_btn_click");
    jQuery("#submit_btn").val("מכין תפריטים");



    jQuery.ajax({
        type: "POST",
        url: "/wp/wp-admin/admin-ajax.php",

        data: {
            "action": "Poll_Ajax_Call",
            "data": data

        },

        success: function (response, status, jqXHR) {
            jQuery(".form_wrapper").slideToggle(500);
            jQuery(".menus_for_you").html(response);
            jQuery("#poll_calc_btn").removeClass("poll_calc_btn_click");
            jQuery("#poll_calc_btn").addClass("poll_calc_btn");
            jQuery("#submit_btn").val("תראו לי תפריטים!");
            jQuery("#back_to_poll").removeClass("dn");
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: jQuery("#back_to_poll").offset().top
            }, 500);

        }
    });


}));