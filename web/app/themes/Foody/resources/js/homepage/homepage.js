/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({grid: '.recipes-grid',cols:3});

    if (foodyGlobals.isMobile) {

        let $mobileFilterBtn = $('.filter-mobile');
        let $mobileFilter = $('.mobile-filter');
        let $closeBtn = $('.close', $mobileFilter);

        if ($mobileFilterBtn.length) {
            $mobileFilterBtn.click((event) => {
                event.stopPropagation();
                $mobileFilter.addClass('open');

                $(window).click(closeMobileFilter);

                $mobileFilter.click(function (event) {
                    event.stopPropagation();
                });

                $('body').addClass('side-active');
            });

            $closeBtn.click(closeMobileFilter);


        }


        function closeMobileFilter() {
            $mobileFilter.removeClass('open');
            $('body').removeClass('side-active');
            document.removeEventListener('click', closeMobileFilter)
        }


        // fades the floating filter button in/out
        // and hides/shows based on scroll to make sure
        // no content is blocked
        $(window).scroll(function () {
            let threshold = 200; // number of pixels before bottom of page that you want to start fading
            let op = (($(document).height() - $(window).height()) - $(window).scrollTop()) / threshold;
            if (op <= 0) {
                $mobileFilterBtn.hide();
            } else {
                $mobileFilterBtn.show();
            }
            $mobileFilterBtn.css("opacity", op);
        });
    }

});