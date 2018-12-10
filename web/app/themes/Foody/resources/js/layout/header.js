let toggleScreenLock = require('../common/screenLock');
let headerScroll = require('./mobile-header-scroll');
jQuery(document).ready(function ($) {


    let $header = $('#masthead');
    let channelsMenu = $('.channels-menu');
    let navbar = $('#foody-navbar-collapse');
    let navAccordion = $('.foody-accordion', navbar);

    let navbarShown = false;

    navbar.on('show.bs.collapse', function (e) {


        if (e.target && e.target.id == 'foody-navbar-collapse') {
            // headerScroll.disableScroller();
            toggleScreenLock(true, '#foody-navbar-collapse');
            navbarShown = true;
        }

    });

    navbar.on('hide.bs.collapse', function (e) {
        console.log(e.target);
        if (e.target && e.target.id == 'foody-navbar-collapse') {
            // headerScroll.enableScroller();
            toggleScreenLock(false, '#foody-navbar-collapse');
            navbarShown = false;
        }

    });

    window.addEventListener("orientationchange", function () {
        if (foodyGlobals.isTablet && navbarShown) {
            if (screen.orientation.angle == 90) {
                $('.navbar-toggler').click();
            }
        }
    });

    // channelsMenu.on('show.bs.collapse', function () {
    //     toggleScreenLock(true);
    // });
    //
    // channelsMenu.on('hide.bs.collapse', function () {
    //     toggleScreenLock(false);
    // });


    let autocomplete = require('../common/autocomplete');

    autocomplete('.search-autocomplete', {});


    let $searchOverlay = $('.search-overlay');
    autocomplete('.search-overlay .search-autocomplete', {});


    $('.btn-search').on('click', function () {
        $searchOverlay.addClass('open');
        // toggleScreenLock(true,$searchOverlay);
        $('input', $searchOverlay).focus();
    });

    $('.close', $searchOverlay).on('click', () => {
        // toggleScreenLock(false,$searchOverlay);
        $searchOverlay.removeClass('open');
    });

    $('.search-overlay .search-autocomplete', $header).keyup((e) => {
        let key = e.which;
        if (key == 13) {
            let search = $(e.target).val();
            window.location = '/?s=' + search;
            return false;
        }
    });

    window.showLoginModal = function () {
        $('#login-modal').modal('show');
    };


    if (foodyGlobals.isMobile === false || (foodyGlobals.isTablet && $(document).width() >= 1024)) {
        $('.menu-item-has-children').mouseenter(function () {

            let $current = $('.dropdown-menu', $(this));
            $current.addClass('show').mouseleave(function () {
                $(this).removeClass('show');
            });

            let currentId = $(this).attr('id');

            $('.dropdown-menu', '.menu-item-has-children:not(#' + currentId + ')').removeClass('show');
        }).mouseleave(function () {
            $(this).removeClass('show');
        });

    }


    let $dropdownMenu = $('.site-header .dropdown-menu');
    //
    // $dropdownMenu.addClass('show');
    // $dropdownMenu.css(
    //     {
    //         opacity: 0
    //     }
    // );
    //
    // // all items inside a dropdown menu
    // let $dropdownItems = $('.dropdown-menu-innner, .dropdown-menu > .dropdown-toggle');
    //
    //
    // // group dropdown items by their left offset - their distance
    // // from the parent
    // let columns = _.groupBy($dropdownItems.toArray(), function (item) {
    //     return jQuery(item).offset().left;
    // });
    //
    //
    // let columnsWidths = Object.keys(columns);
    //
    // // if more than one column we
    // // need to remove the left border
    // // from the links
    // if (columnsWidths.length > 1) {
    //
    //
    //     columnsWidths.forEach((column, i) => {
    //         if (i > 0) {
    //
    //             let columnElements = columns[column];
    //             // last column, remove border
    //             if (i == columnsWidths.length - 1) {
    //
    //                 // remove border from the column
    //                 // and the links inside it
    //                 $(columnElements).each(function () {
    //                     $(this).css('border', 'none');
    //                     $('a,.toggle-wrap', this).css('border', 'none');
    //                 });
    //             }
    //
    //             // // all but first
    //             // $('> a,.toggle-wrap', columnElements).each(function () {
    //             //     $(this).css('padding-right', '8px');
    //             // });
    //
    //         }
    //     });
    //
    // }
    //
    // $dropdownMenu.attr('style', '');
    // $dropdownMenu.removeClass('show');


    $dropdownMenu.on('show.bs.collapse', function (e) {

        $(e.target).prev('.toggle-wrap').addClass('open');

    });

    $dropdownMenu.on('hide.bs.collapse', function (e) {

        $(e.target).prev('.toggle-wrap').removeClass('open');

    });


    /*
     *
     * TODO
     if (foodyGlobals.isMobile === false || (foodyGlobals.isTablet && $(document).width() >= 1024)) {
     $('.menu-item-has-children').mouseenter(function () {


     $('> li','#menu-navbar').addClass('children-shown');

     let $current = $('.dropdown-menu', $(this));
     $current.addClass('show')
     .mouseleave(function () {
     $(this).removeClass('show');
     });

     let currentId = $(this).attr('id');

     $('.dropdown-menu','.menu-item-has-children:not(#'+currentId+')').removeClass('show');
     }).mouseleave(function () {
     let $current = $('.dropdown-menu', $(this));
     $current.removeClass('show');
     $('> li','#menu-navbar').removeClass('children-shown');
     });

     }
     * */

});