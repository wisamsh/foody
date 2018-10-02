/**
 * Created by moveosoftware on 5/15/18.
 */


require('./team');
require('./recommended');
require('./categories');

// $('#dw-p2').bmdDrawer();

// $('.drawer-nav').drawer();


jQuery(document).ready(($) => {

    if (foody_globals.isMobile) {

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
    }

});