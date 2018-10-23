/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    // sidebar filter
    let filter = new FoodySearchFilter({
        selector: '.homepage #accordion-foody-filter',
        grid: '#homepage-feed',
        cols: 3,
        searchButton: '.show-recipes'
    });

    // search and filter pager
    let pager = new FoodyContentPaging({
        context: 'homepage',
        contextArgs: [],
        filter: filter,
        sort:'#sort-homepage-feed'
    });


    // mobile filter handlers
    if (foodyGlobals.isMobile) {

        let $mobileFilterBtn = $('.filter-mobile');
        let $mobileFilter = $('.mobile-filter');
        let $closeBtn = $('.close', $mobileFilter);

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