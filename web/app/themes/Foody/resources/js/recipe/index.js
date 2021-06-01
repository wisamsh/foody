/**
 * Created by moveosoftware on 6/27/18.
 */
window.EXIF = require('exif-js');
jQuery(document).ready(function () {
    window.scroller = require('../common/scroll-progress');

    var recipe_categories = foodyGlobals['post']['categories']
    var check_recipe = recipe_categories.some((categorie) => categorie.name === 'עוגות')
    if ( check_recipe ){
        require('./recipe');
    } else {
        require('./recipe-old');
    }

    require('./recipe-analytics');
    require('./foody-calculator');

    calculator('.recipe-ingredients-container li:not(.free-text-ingredients) .amount');
});