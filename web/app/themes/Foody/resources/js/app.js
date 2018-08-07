/**
 * Created by moveosoftware on 5/4/18.
 */

let IScroll = require('iscroll');
// let $ = require('jquery');
require('jquery-drawer');
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


/*
 * Pages
 * */

require('./homepage');

require('./team');

require('./recipe');

jQuery(document).ready(function ($) {
    $('body').bootstrapMaterialDesign();
    $('.foody-select').selectpicker({dropdownAlignRight: true, style: 'foody-select'});
    $('.foody-rating').rating({filledStar:'<i class="icon-big-star-rank filled"></i>',emptyStar:'<i class="icon-big-star-rank"></i>'})
});