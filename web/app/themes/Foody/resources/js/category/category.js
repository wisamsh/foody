/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({
        grid: '#category-feed',
        cols:2,
        page:'body[class~=category]',
        context: 'category',
        contextArgs: [
            foodyGlobals.objectID
        ],
    });

    let categoryPager = new FoodyContentPaging({
        context: 'category',
        contextArgs: [
            foodyGlobals.objectID
        ],
        filter: filter,
        sort:'#sort-category-feed'
    });


});