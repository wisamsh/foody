import('jquery-validation/dist/jquery.validate.min');
import('block-ui');
import('autocomplete.js/dist/autocomplete.jquery.min');
import('bootstrap-material-design/dist/js/bootstrap-material-design.min').then(() => {
    import('bootstrap-select/dist/js/bootstrap-select.min');
});

import('slick-carousel/slick/slick.min');
window._ = import('underscore/underscore-min');
window.foodyFormMessage = import('./common/cf7-messages');

window.lottie = import('lottie-web/build/player/lottie.min');
alert('a');
/*
 * Commmon
 * */
import('./common');
import('./components');
import('./layout');
import('./feed-filter');
import('./search');
// Tag Manager
import('./common/tag-manager').then((TagManager) => {
    window.tagManager = new TagManager.default();
});


jQuery(document).ready(function ($) {

    let $body = $('body');
    // noinspection JSUnresolvedFunction
    $body.bootstrapMaterialDesign();
    // $.fn.selectpicker.Constructor.BootstrapVersion = '4';
    $('.foody-select').selectpicker({dropdownAlignRight: true, style: 'foody-select', dropupAuto: false, width: 'fit'});

    $('[data-toggle="popover"]').popover();

    $('.foody-slider').slick();

    import('./common/analytics').then((FoodyAnalytics)=>{
        window.analytics = new FoodyAnalytics.default();
        analytics.view();
    });





    $('[data-toggle="tooltip"]').tooltip({
        'container': 'body'
    });

    $('header .navbar-toggler').on('click', () => {
        $('.quadmenu-navbar-toggle').click();
    });

    $body.on('click', '.tooltip .close', function () {
        $(this).closest('.tooltip').tooltip('hide')
    });

    let $seo = $('#seo');

    if ($seo.length) {
        let height = $seo.height();
        $('article.content').css('padding-bottom', height + 'px');
    }

    window.onbeforeunload = function (event) {
        if (event.currentTarget.performance.navigation.type == 2) {
            foodyAjax({
                action: 'foody_back_button',
                data: {
                    back_button: true
                }
            }, function (err, data) {
                if (err) {
                    console.log('err: ', err);
                } else {
                    console.log('data: ', data);
                }
            });
        }
    }


    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            return this.optional(element) || regexp.test(value);
        }
    );

    $.validator.addMethod(
        "password",
        function (value) {

            let hasNumbers = /[0-9]+/.test(value);
            let nonEn = /[^a-z0-9]/i.test(value);

            return hasNumbers && nonEn === false;
        }
    );
});