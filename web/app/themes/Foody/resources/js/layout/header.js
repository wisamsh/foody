let toggleScreenLock = require('../common/screenLock');
require('./mobile-header-scroll');
jQuery(document).ready(function ($) {

    let initialScrollEvent = true;
    let $header = $('#masthead');
    let navbar = $('#foody-navbar-collapse');

    let navbarShown = false;
    let bannerPoped = false;

    let timeIdle = 0;

    let englishRgx = /^[a-zA-Z\- _]+$/;

    if ($('.user-name-header').length && englishRgx.test($('.user-name-header')[0].innerText)) {
        $('.user-name-header').attr('style', 'direction: ltr');
    }

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
            if (search) {
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
        $('.navbar-btn.accessibility').on('click', function (e) {
            e.stopPropagation();
            $(foodyGlobals.custom_accessibility_class).click();
        });
    }

    //popup banner
    $(window).on('scroll', function () {
        if (!sessionStorage.getItem('banner-popup-closed')) {
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

    $('footer #popup-banner .close').on('click', function () {
        if ($('.sticky_bottom_header').length) {
            $('.sticky_bottom_header').css("bottom", "0");
            $('.sticky_bottom_header #quadmenu').css("bottom", "8%");
            $('.sticky_bottom_header .navbar-header').css("bottom", "53vh");
            $('.related-content-overlay .related-recipes-container').css("bottom", "65px");
        }
        sessionStorage.setItem('banner-popup-closed', 'true');
    });

    $('#popup-banner').on('shown.bs.modal', function () {
        if ($('.sticky_bottom_header').length) {
            let newBottomHeader = $('#popup-banner .modal-dialog').height();
            let newBottomMenu = newBottomHeader + parseFloat($('.sticky_bottom_header #quadmenu').css('bottom'));
            let newBottomMenuHeader = newBottomHeader + $(window).height() * 0.53;
            let newBottomRelatedRecipes = newBottomHeader + 65;
            $('.sticky_bottom_header').css("bottom", newBottomHeader);
            $('.sticky_bottom_header #quadmenu').css("bottom", newBottomMenu);
            $('.sticky_bottom_header .navbar-header').css("bottom", newBottomMenuHeader);
            $('.related-content-overlay .related-recipes-container').css("bottom", newBottomRelatedRecipes);

        }
        $(document).off('focusin.modal');
    });

    // if($('#newsletter-modal').length) {
    //     setTimeout(function () {
    //         // load popup after 5 second
    //         showNewsletterModal();
    //     }, 5000);
    // }
    if ($('#newsletter-modal').length) {
        $(window).on('scroll', function () {
            timeIdle = 0;
        });
    }

    if ($('#newsletter-modal').length) {
        let interval = setInterval(function () {
            timeIdle++;
            if (timeIdle == 10) {
                showNewsletterModal();
            }
            if (sessionStorage.getItem('newsletter-popup-closed')) {
                clearInterval(interval);
            }
        }, 1000);
    }

    $('#newsletter-modal').on('show.bs.modal', function () {
        $('#newsletter-modal .modal-body > p').remove();
    });

    $('#newsletter-modal').on('hidden.bs.modal', function () {
        $('#newsletter-modal').attr('style', 'display: none !important');
        sessionStorage.setItem('newsletter-popup-closed', 'true');
    });

    $('#newsletter-modal .wpcf7-form .wpcf7-submit').on('click', function () {
        let isEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if ((typeof $('#newsletter-modal .wpcf7-form .wpcf7-email')[0] != "undefined") &&
            ($('#newsletter-modal .wpcf7-form .wpcf7-email')[0].value != '') &&
            (isEmail.test($('#newsletter-modal .wpcf7-form .wpcf7-email')[0].value))) {
            let seconds = 0;
            let submitInterval = setInterval(function () {
                seconds++;
                if (seconds == 4) {
                    $('#newsletter-modal').modal('hide');
                }
                if (sessionStorage.getItem('newsletter-popup-closed')) {
                    clearInterval(submitInterval);
                }
            }, 1000);
        }
    });


    if ($('.phone-link').length) {
        if (window.innerWidth > 736) {
            $('.phone-link').each(function (index, value) {
                let number = value.innerText;
                let numberDiv = $('<span style="white-space: nowrap;">' + number + '</span>');
                numberDiv.insertAfter(value);
                value.remove();
            });
        } else {
            $('.phone-link').attr('style', 'white-space: nowrap');
        }
    }

    /** new mobile header **/
    let switchedToHamburgerLogo = false;
    let closedLogoIsShown = false;
    let foodyTextLogoIsShow = false;
    let onLoadScroll = false;
    let oldPageYOffset = 0;

    if($(window).scrollTop() < 110){
        foodyTextLogoIsShow = true;
    }

    if ($('.social-btn-container').length && $('.social-buttons-container').length) {
        $('.social-btn-container').on('click', function () {
            $('.social-buttons-container').toggleClass('hidden');
        });
    }

    if ($('.related-content-btn').length && $('.related-content-overlay').length && $('.similar-content-items').length) {
        $('.related-content-btn').on('click', function () {
            $('.related-content-overlay').toggleClass('open');
        });
    }

    if ($('.sticky_bottom_header .navbar-toggler.custom-logo-link').length) {
        $('.sticky_bottom_header .navbar-toggler.custom-logo-link').on('click', function (e) {
            if (!switchedToHamburgerLogo || foodyTextLogoIsShow) {
                window.location.href = window.location.origin;
                return;
            }
            if ($('.sticky_bottom_header .quadmenu-navbar-collapse').length) {
                let expended = $('.sticky_bottom_header .quadmenu-navbar-collapse').prop('.aria-expanded');
                $('.sticky_bottom_header .quadmenu-navbar-collapse').prop('.aria-expanded', !expended);
                $('.sticky_bottom_header .quadmenu-navbar-collapse').toggleClass('in');


                if ($('.navbar-overlay').length && $('.navbar-header').length) {
                    $('.navbar-overlay').toggleClass('hidden');
                    $('.navbar-header').toggleClass('hidden');
                }

                if (!expended) {
                    $('.sticky_bottom_header #quadmenu').css('height', '45%');
                    $('.sticky_bottom_header #quadmenu')[0].scrollTop = 0;
                    // let closeImage = '<img class="foody-logo-text logo-close" src="' + foodyGlobals.imagesUri + "close-menu-logo.svg" + '">';
                    // $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-text').replaceWith(closeImage);
                    $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-hamburger').toggleClass('hidden');
                    $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-close').toggleClass('hidden');
                    $('.sticky_bottom_header .foody-navbar-container .navbar-container').toggleClass('hidden');
                    closedLogoIsShown = true;
                } else {
                    // let hamburgerImage = '<img class="foody-logo-text logo-hamburger" src="' + foodyGlobals.imagesUri + "hamburger.svg" + '">';
                    // $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-text').replaceWith(hamburgerImage);
                    $('.sticky_bottom_header .foody-navbar-container .navbar-container').toggleClass('hidden');
                    $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-close').toggleClass('hidden');
                    $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-hamburger').toggleClass('hidden');
                    closedLogoIsShown = false;

                    $('.sticky_bottom_header #quadmenu').css('height', '0');
                }
            }
        });
    }

    if ($('.sticky_bottom_header').length) {
        $(window).on('scroll', function () {
            if($(window).scrollTop() < 110){
                if(switchedToHamburgerLogo && !foodyTextLogoIsShow) {
                    if(!closedLogoIsShown) {
                        //         let foodyTextImage = '<img class="foody-logo-text" src="' + foodyGlobals.imagesUri + "foody_logo-with-white.svg" + '">';
                        //         $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-text').replaceWith(foodyTextImage);
                        $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-hamburger').toggleClass('hidden');
                        $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-text').toggleClass('hidden');
                        switchedToHamburgerLogo = false;
                    }
                }
            }
            else{
            //     let hamburgerImage = '<img class="foody-logo-text logo-hamburger" src="' + foodyGlobals.imagesUri + "hamburger.svg" + '">';
            //     $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-text').replaceWith(hamburgerImage);
                if(!switchedToHamburgerLogo) {
                    $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-text').toggleClass('hidden');
                    $('.sticky_bottom_header .site-branding .custom-logo-link .foody-logo-hamburger').toggleClass('hidden');
                    switchedToHamburgerLogo = true;
                    foodyTextLogoIsShow = false;
                }
            }
        });
    }
});
