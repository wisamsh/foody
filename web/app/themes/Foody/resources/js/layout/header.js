let toggleScreenLock = require('../common/screenLock');
require('./mobile-header-scroll');
jQuery(document).ready(function ($) {


    let $header = $('#masthead');
    let navbar = $('#foody-navbar-collapse');

    let navbarShown = false;
    let bannerPoped = false;

    navbar.on('show.bs.collapse', function (e) {


        if (e.target && e.target.id === 'foody-navbar-collapse') {
            toggleScreenLock(true, '#foody-navbar-collapse');
            navbarShown = true;
        }

    });

    navbar.on('hide.bs.collapse', function (e) {
        console.log(e.target);
        if (e.target && e.target.id === 'foody-navbar-collapse') {
            toggleScreenLock(false, '#foody-navbar-collapse');
            navbarShown = false;
        }

    });

    window.addEventListener("orientationchange", function () {
        if (foodyGlobals.isTablet && navbarShown) {
            if (screen.orientation.angle === 90) {
                $('.navbar-toggler').click();
            }
        }
    });

    let autocomplete = require('../common/autocomplete');

    autocomplete('.search-autocomplete', {});


    let $searchOverlay = $('.search-overlay');
    autocomplete('.search-overlay .search-autocomplete', {});


    $('.btn-search').on('click', function () {
        $searchOverlay.addClass('open');
        $('input', $searchOverlay).focus();
    });

    $('.close', $searchOverlay).on('click', () => {
        $searchOverlay.removeClass('open');
    });

    $('.search-overlay .search-autocomplete', $header).keyup((e) => {
        let key = e.which;
        if (key === 13) {
            let search = $(e.target).val();
            if(search){
                window.location = '/?s=' + search;
            }
            return false;
        }
    });

    window.showLoginModal = function () {
        $('#login-modal').modal('show');
    };

    window.showNewsletterModal = function () {
        $('#newsletter-modal').modal('show');
    };

    // Click custom accessibility widget
    if (foodyGlobals.show_custom_accessibility && foodyGlobals.custom_accessibility_class) {
        $('.navbar-btn.accessibility').on('click',function (e) {
            e.stopPropagation();
            $(foodyGlobals.custom_accessibility_class).click();
        });
    }

    //popup banner
    $(window).on('scroll', function () {
        if(!sessionStorage.getItem('popup-closed')) {
            let popupBanner = $('#popup-banner');
            if (popupBanner.length && !bannerPoped) {
                popupBanner.modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-open').css('overflow', 'auto');
                $('.modal-backdrop').css('display', 'none');
                popupBanner.slideDown();
                popupBanner.modal('show');
                // let bottomScale = ($(window).width() < 575) ? '10%' : '15%';
                // $('#popup-banner .modal-dialog').css('bottom', bottomScale);
                bannerPoped = true;
            }
        }
    });

    $('footer .close').on('click',function () {
        sessionStorage.setItem('popup-closed','true');
    });

    $('#popup-banner').on('shown.bs.modal', function() {
        $(document).off('focusin.modal');
    });

    if($('#newsletter-modal').length) {
        setTimeout(function () {
            // load popup after 30 second
            showNewsletterModal();
        }, 30000);
    }

});