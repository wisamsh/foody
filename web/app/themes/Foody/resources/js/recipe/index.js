/**
 * Created by moveosoftware on 6/27/18.
 */
window.EXIF = require('exif-js');
jQuery(document).ready(function () {
    window.scroller = require('../common/scroll-progress');

    require('./recipe');
    require('./recipe-analytics');
    require('./foody-calculator');

    calculator('.recipe-ingredients-container li:not(.free-text-ingredients) .amount');
});