/**
 * Created by moveosoftware on 6/27/18.
 */
window.EXIF = import('exif-js');
jQuery(document).ready(function () {
    window.scroller = import('../common/scroll-progress');

    import('./recipe');
    import('./recipe-analytics');
    import('./foody-calculator');

    calculator('.recipe-ingredients-container li:not(.free-text-ingredients) .amount');
});