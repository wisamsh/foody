/**
 * Created by moveosoftware on 10/1/18.
 */

jQuery(document).ready(($) => {

    let arrows = $('.foody-accordion .arrow');
    let titles = $('.foody-accordion .foody-accordion-title h5');


    arrows.click(function () {

        let target = $(this).attr('aria-controls');


        $('a[href="#' + target + '"]').click();
    });
    //
    // titles.click(function (e) {
    //
    //
    //     e.stopPropagation();
    //     $('a',this).click();
    //
    // });


});