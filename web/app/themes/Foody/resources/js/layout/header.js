jQuery(document).ready(function ($) {

    let channelsMenu=  $('.channels-menu');

    channelsMenu.on('show.bs.collapse', function () {
        $('body').css('overflow', 'hidden');
    });

    channelsMenu.on('hide.bs.collapse', function () {
        $('body').css('overflow', 'visible');
    });

});