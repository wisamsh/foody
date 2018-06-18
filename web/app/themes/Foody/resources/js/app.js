/**
 * Created by moveosoftware on 5/4/18.
 */

let IScroll = require('iscroll');
let $ = require('jquery');
require('jquery-drawer');
require('bootstrap');
require('bootstrap-material-design');
require('bootstrap-select');
// require('bootstrap-material-design/js/');
require('slick-carousel');
require('../sass/app.scss');


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

require('./components');


/*
 * Pages
 * */

require('./homepage');

jQuery(document).ready(function ($) {
    $('body').bootstrapMaterialDesign();
    $('.foody-select').selectpicker({dropdownAlignRight: true, style: 'foody-select'});
});