jQuery(document).ready(function ($) {


    let $header = $('#masthead');
    let channelsMenu = $('.channels-menu');
    let navbar = $('#foody-navbar-collapse');
    let navAccordion = $('.foody-accordion', navbar);

    navbar.on('show.bs.collapse', function () {
        $('body').addClass('lock');
    });

    navbar.on('hide.bs.collapse', function (e) {
        console.log(e.target);
        if (e.target && e.target.id == 'foody-navbar-collapse') {
            $('body').removeClass('lock');
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


});