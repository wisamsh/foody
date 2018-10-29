jQuery(document).ready(function ($) {


    let $header = $('#masthead');
    let channelsMenu = $('.channels-menu');
    let navbar = $('#foody-navbar-collapse');
    let navAccordion = $('.foody-accordion', navbar);

    let navbarShown = false;

    navbar.on('show.bs.collapse', function () {
        $('body').addClass('lock');
        navbarShown = true;
    });

    navbar.on('hide.bs.collapse', function (e) {
        console.log(e.target);
        if (e.target && e.target.id == 'foody-navbar-collapse') {
            $('body').removeClass('lock');

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

    channelsMenu.on('show.bs.collapse', function () {
        $('body').css('overflow', 'hidden');
    });

    channelsMenu.on('hide.bs.collapse', function () {
        $('body').css('overflow', 'visible');
    });


    let autocomplete = require('../common/autocomplete');

    autocomplete('.search-autocomplete', {});


    let $searchOverlay = $('.search-overlay');
    autocomplete('.search-overlay .search-autocomplete', {});


    $('.btn-search').click(function () {
        $searchOverlay.addClass('open');
        $('body').addClass('lock');
        $('input', $searchOverlay).focus();
    });

    $('.close', $searchOverlay).click(() => {
        $('body').removeClass('lock');
        $searchOverlay.removeClass('open');
    });

    $('.search-overlay .search-autocomplete', $header).keyup((e) => {
        let key = e.which;
        if (key == 13) {
            let search = $(e.target).val();
            window.location.href = '?s=' + search;
            return false;
        }
    });

    window.showLoginModal = function () {
        $('#login-modal').modal('show');
    }

});