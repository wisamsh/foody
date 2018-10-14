/**
 * Created by moveosoftware on 5/4/18.
 */

let IScroll = require('iscroll');
// let $ = require('jquery');
require('jquery-drawer');
require('jquery-validation');
require('autocomplete.js/src/jquery/plugin.js');
require('bootstrap');
require('bootstrap-material-design');
require('bootstrap-select');
require('bootstrap-star-rating');
require('./plugins');

// require('bootstrap-material-design/js/');
require('slick-carousel');
require('../sass/app.scss');
window._ = require('underscore');
window.Fraction = require('fractional').Fraction;
window.lottie = require('lottie-web');


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

jQuery(document).ready(function ($) {
    $('body').bootstrapMaterialDesign();
    $('.foody-select').selectpicker({dropdownAlignRight: true, style: 'foody-select'});
    $('[data-toggle="popover"]').popover();
    $('.foody-rating').rating({
        filledStar: '<i class="icon-big-star-rank filled"></i>',
        emptyStar: '<i class="icon-big-star-rank"></i>',
        containerClass:'foody-rating-container'
    });

    $('.foody-slider').slick();

});