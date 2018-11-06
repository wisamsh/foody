/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');
let toggleScreenLock = require('../common/screenLock');

jQuery(document).ready(($) => {

    // sidebar filter
    let filter = new FoodySearchFilter({
        selector: '.homepage #accordion-foody-filter',
        grid: '#homepage-feed',
        cols: 2,
        searchButton: '.show-recipes'
    });

    // search and filter pager
    let pager = new FoodyContentPaging({
        context: 'homepage',
        contextArgs: [],
        filter: filter,
        sort: '#sort-homepage-feed'
    });


    // TODO move to common
    // mobile filter handlers
    if (foodyGlobals.isMobile) {

        let $mobileFilterBtn = $('.filter-mobile');
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
                $mobileFilter.addClass('open');

                $(window).click(function () {
                    closeMobileFilter(false);
                });

                $mobileFilter.click(function (event) {
                    event.stopPropagation();
                });

                toggleScreenLock(true,$mobileFilter,true);
                filterShown = true;
            });

            $closeBtn.click(function () {
                closeMobileFilter(true);
            });


        }


        function closeMobileFilter(clear) {
            $mobileFilter.removeClass('open');
            toggleScreenLock(false,$mobileFilter,true);
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
            let threshold = 200; // number of pixels before bottom of page that you want to start fading
            let op = (($(document).height() - $(window).height()) - $(window).scrollTop()) / threshold;
            if (op <= 0) {
                $mobileFilterBtn.hide();
            } else {
                $mobileFilterBtn.show();
            }
            $mobileFilterBtn.css("opacity", op);
        });


        window.addEventListener("orientationchange", function () {
            if (foodyGlobals.isTablet && filterShown) {
                if (screen.orientation.angle == 90) {
                    closeMobileFilter();
                }
            }
        });
    }

});