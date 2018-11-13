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
            toggleScreenLock(true,'#foody-navbar-collapse');
            navbarShown = true;
        }

    });

    navbar.on('hide.bs.collapse', function (e) {
        console.log(e.target);
        if (e.target && e.target.id == 'foody-navbar-collapse') {
            // headerScroll.enableScroller();
            toggleScreenLock(false,'#foody-navbar-collapse');
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


    $('.btn-search').on('click',function () {
        $searchOverlay.addClass('open');
        // toggleScreenLock(true,$searchOverlay);
        $('input', $searchOverlay).focus();
    });

    $('.close', $searchOverlay).on('click',() => {
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

});