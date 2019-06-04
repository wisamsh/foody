/**
 * Created by moveosoftware on 7/2/18.
 */
//
// import('jquery-validation/dist/jquery.validate.min');
// import('block-ui');
// import('autocomplete.js/dist/autocomplete.jquery.min');
// import('bootstrap-material-design/dist/js/bootstrap-material-design.min');
// import('bootstrap-select/dist/js/bootstrap-select.min');
//
// import('slick-carousel');
// window._ = import('underscore/underscore-min');
//
//
// window.foodyFormMessage = import('./cf7-messages');
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
//     let FoodyAnalytics = import('./analytics');
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

import('./forms');
import('./foody-grid');
import('./foody-ajax');
import('./comments');
import('./how-i-did');
import('./favorites');
import('./follow');
import('./sort');
import('./foody-search-filter');
import('./social');
import('./mobile-filter');
import('./tag-manager');
