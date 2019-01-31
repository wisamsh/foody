/**
 * Created by moveosoftware on 5/4/18.
 */

window.env = require('dotenv').config();


// let IScroll = require('iscroll');
// let $ = require('jquery');
// require('jquery-drawer');
require('jquery-validation');
require('block-ui');
require('autocomplete.js/src/jquery/plugin.js');
// require('bootstrap');
require('bootstrap-material-design');
require('bootstrap-select');
require('bootstrap-star-rating');
require('./plugins');


require('slick-carousel');
window._ = require('underscore');
window.Fraction = require('fractional').Fraction;
window.lottie = require('lottie-web');

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

        return hasNumbers && nonEn == false;
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

require('./team');

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

window.foodyFormMessage = require('./common/cf7-messages');



jQuery(document).ready(function ($) {

    $('body').bootstrapMaterialDesign();
    // $.fn.selectpicker.Constructor.BootstrapVersion = '4';
    $('.foody-select').selectpicker({dropdownAlignRight: true, style: 'foody-select',dropupAuto:false,width:'fit'});


    $('[data-toggle="popover"]').popover();

    $('.foody-rating').rating({
        filledStar: '<i class="icon-big-star-rank filled"></i>',
        emptyStar: '<i class="icon-big-star-rank"></i>',
        containerClass: 'foody-rating-container'
    });

    $('.foody-slider').slick();

    let FoodyAnalytics = require('./common/analytics');

    window.analytics = new FoodyAnalytics();

    analytics.view();
    $('[data-toggle="tooltip"]').tooltip({
        'container':'body'
    });

    $('header .navbar-toggler').on('click',()=>{
        $('.quadmenu-navbar-toggle').click();
    });

    $('body').on('click','.tooltip .close',function () {
        $(this).closest('.tooltip').tooltip('hide')
    });

    foodyFormMessage('.newsletter');

});