jQuery(document).ready(function ($) {

    let channelsMenu = $('.channels-menu');

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
        $('input', $searchOverlay).focus();
    });

    $('.close', $searchOverlay).click(() => {
        $searchOverlay.removeClass('open');
    })


});