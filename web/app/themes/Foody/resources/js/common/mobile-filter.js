/**
 * Created by moveosoftware on 12/23/18.
 */
let toggleScreenLock = require('./screenLock');

jQuery(document).ready(($) => {
    // mobile filter handlers
    if (foodyGlobals.isMobile) {
        let $mobileFilterBtn = $('.filter-mobile');
        let $mobileWhatsappIcon = $('#whatsapp.whatsapp-mobile');
        if (foodyGlobals.hideFilter) {
            $mobileFilterBtn.remove();
        } else {

            let $mobileFilter = $('.mobile-filter');
            let $closeBtn = $('.close', $mobileFilter);
            let filterShown = false;
            if ($mobileFilterBtn.length) {
                $('.md-checkbox').on('click', (e) => {
                    e.stopPropagation();
                });

                $('.show-recipes', $mobileFilter).click(function () {
                    closeMobileFilter();
                });
                $mobileFilterBtn.click((event) => {
                    event.stopPropagation();

                    // if brands avenue is open => close it
                    if($('.brands-toggle-mobile .brands-avenue-mobile').length){
                        if($('.brands-toggle-mobile .brands-avenue-mobile').hasClass('open')){
                            $('.brands-avenue-mobile').removeClass('open');
                        }
                    }

                    $mobileFilter.addClass('open');

                    $(window).click(function () {
                        closeMobileFilter(false);
                    });

                    $mobileFilter.click(function (event) {
                        event.stopPropagation();
                    });

                    toggleScreenLock(true, $mobileFilter, true);
                    filterShown = true;
                });

                $closeBtn.click(function () {
                    closeMobileFilter(true);
                });


            }


            function closeMobileFilter(clear) {
                $mobileFilter.removeClass('open');
                toggleScreenLock(false, $mobileFilter, true);
                document.removeEventListener('click', closeMobileFilter);
                filterShown = false;
                if (clear) {
                    clearFilter();
                }
            }

            function clearFilter() {
                $('.md-checkbox input[type="checkbox"]:checked', $mobileFilter).each(function () {

                    $(this).next('label').click();
                });
            }


            // fades the floating filter button in/out
            // and hides/shows based on scroll position to make sure
            // no content is blocked
            $(window).scroll(function () {
                let threshold = 600; // number of pixels before bottom of page that you want to start fading
                let op = (($(document).height() - $(window).height()) - $(window).scrollTop()) / threshold;
                let pointerEvents = "all";

                if (op <= 0.2) {
                    op = 0;
                    pointerEvents = "none"
                }
                $mobileFilterBtn.css("opacity", op);
                $mobileFilterBtn.css("pointer-events", pointerEvents);
                $mobileWhatsappIcon.css("opacity", op);
                $mobileWhatsappIcon.css("pointer-events", pointerEvents);
            });


            window.addEventListener("orientationchange", function () {
                if (foodyGlobals.isTablet && filterShown) {
                    if (screen.orientation.angle == 90) {
                        closeMobileFilter();
                    }
                }
            });
        }


    }
});

