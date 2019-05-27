// noinspection JSUnresolvedFunction
/**
 * Created by moveosoftware on 5/4/18.
 */

window.env = require('dotenv').config();
require('jquery-validation/dist/jquery.validate.min');
require('block-ui');
require('autocomplete.js/dist/autocomplete.jquery.min');
require('bootstrap-material-design/dist/js/bootstrap-material-design.min');
require('bootstrap-select/dist/js/bootstrap-select.min');
require('slick-carousel');
window._ = require('underscore/underscore-min');
window.foodyFormMessage = require('./common/cf7-messages');

// require('autocomplete.js/src/jquery/plugin.js');


// window.Fraction = require('fractional').Fraction;
setTimeout(() => {
    window.lottie = require('lottie-web');
});

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


// /*
//  * Plugins
//  * */
//
// require('./plugins');
//
//
//
/*
 * Commmon
 * */

require('./common');

require('./components');

require('./layout');

/*
 * Pages
 * */

require('./homepage');
require('./white-label');

require('./team');
require('./article');
require('./recipe');
require('./playlist');
require('./profile');
require('./category');
require('./author');
require('./channel');
require('./register');
require('./login');
require('./search');
require('./contact-us');
require('./tag');
require('./items');
require('./feed-filter');
require('./general-pages');

// Tag Manager
const TagManager = require('./common/tag-manager');
window.tagManager = new TagManager();

jQuery(document).ready(function ($) {

    let $body = $('body');
    // noinspection JSUnresolvedFunction
    $body.bootstrapMaterialDesign();
    // $.fn.selectpicker.Constructor.BootstrapVersion = '4';
    $('.foody-select').selectpicker({dropdownAlignRight: true, style: 'foody-select', dropupAuto: false, width: 'fit'});

    $('[data-toggle="popover"]').popover();

    $('.foody-slider').slick();

    let FoodyAnalytics = require('./common/analytics');

    window.analytics = new FoodyAnalytics();

    analytics.view();

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
});