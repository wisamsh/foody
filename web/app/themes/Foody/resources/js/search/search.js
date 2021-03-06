/**
 * Created by moveosoftware on 10/14/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({
        grid: '#search-results',
        gridArgs: {
            titleSelector: '.search .details-container .title'
        },
        cols: 2,
        page: 'body[class~=search]',
        context: 'search',
        contextArgs: []
    });

    let searchPager = new FoodyContentPaging({
        context: 'search',
        contextArgs: [],
        filter: filter,
        sort: '#sort-search-results'
    });


});