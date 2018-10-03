jQuery(document).ready(function ($) {

    let channelsMenu = $('.channels-menu');
    let navbar = $('#foody-navbar-collapse');


    navbar.on('show.bs.collapse', function () {
        $('body').addClass('lock');
    });

    navbar.on('hide.bs.collapse', function () {
        $('body').removeClass('lock');
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
    autocomplete('.search-overlay .search-autocomplete', {

    });


    $('.btn-search').click(function () {
        $searchOverlay.addClass('open');
        $('body').addClass('lock');
        $('input', $searchOverlay).focus();
    });

    $('.close', $searchOverlay).click(() => {
        $('body').removeClass('lock');
        $searchOverlay.removeClass('open');
    })


});