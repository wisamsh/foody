let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({
        grid: '#foody-filter-feed',
        cols: 2,
        page: 'body[class~=foody_filter-template]',
        context: 'foody_filter',
        contextArgs: [
            foodyGlobals.objectID
        ]
    });

    new FoodyContentPaging({
        context: 'foody_filter',
        filter: filter,
        sort: '#sort-foody_filter',
        contextArgs: [
            foodyGlobals.objectID
        ]
    });

});