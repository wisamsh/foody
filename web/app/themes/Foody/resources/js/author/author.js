/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');

jQuery(document).ready(($) => {
    new FoodySearchFilter({selector: '.author #accordion-foody-filter', grid: '.author-recipe-grid', cols: 3});
    new FoodySearchFilter({selector: '.author #accordion-foody-filter', grid: '.author-playlist-grid', cols: 3});
});