/**
 * Created by moveosoftware on 7/2/18.
 */
//
// require('jquery-validation/dist/jquery.validate.min');
// require('block-ui');
// require('autocomplete.js/dist/autocomplete.jquery.min');
// require('bootstrap-material-design/dist/js/bootstrap-material-design.min');
// require('bootstrap-select/dist/js/bootstrap-select.min');
//
// require('slick-carousel');
// window._ = require('underscore/underscore-min');
//
//
// window.foodyFormMessage = require('./cf7-messages');
//
// jQuery(document).ready(function ($) {
//
//     $('body').bootstrapMaterialDesign();
//     // $.fn.selectpicker.Constructor.BootstrapVersion = '4';
//     $('.foody-select').selectpicker({dropdownAlignRight: true, style: 'foody-select', dropupAuto: false, width: 'fit'});
//
//
//     $('[data-toggle="popover"]').popover();
//
//     $('.foody-slider').slick();
//
//     let FoodyAnalytics = require('./analytics');
//
//     window.analytics = new FoodyAnalytics();
//
//     analytics.view();
//     $('[data-toggle="tooltip"]').tooltip({
//         'container': 'body'
//     });
//
//     $('header .navbar-toggler').on('click', () => {
//         $('.quadmenu-navbar-toggle').click();
//     });
//
//     $('body').on('click', '.tooltip .close', function () {
//         $(this).closest('.tooltip').tooltip('hide')
//     });
//
//     foodyFormMessage('.newsletter');
//
//     let $seo = $('#seo');
//
//     if ($seo.length) {
//         let height = $seo.height();
//         $('article.content').css('padding-bottom', height + 'px');
//     }
// });

require('./forms');
require('./foody-ajax');
require('./comments');
require('./how-i-did');
require('./favorites');
require('./follow');
require('./sort');
require('./foody-search-filter');
require('./social');
require('./mobile-filter');
require('./tag-manager');
require('./approvals');
require('./approvals-analytics');
