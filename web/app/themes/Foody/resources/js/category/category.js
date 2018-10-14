/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({grid: '#category-feed',cols:3});

    let categoryPager = new FoodyContentPaging({
        context: 'category',
        contextArgs: [6],
        filter: filter
    });


});