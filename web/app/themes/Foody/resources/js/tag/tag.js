/**
 * Created by moveosoftware on 11/19/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({grid: '#tag-feed',cols:2,page:'body[class~=tag]'});

    new FoodyContentPaging({
        context: 'tag',
        contextArgs: [
            foodyGlobals.objectID
        ],
        filter: filter,
        sort:'#sort-tag-feed'
    });


});