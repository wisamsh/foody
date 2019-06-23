/**
 * Created by moveosoftware on 11/19/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let accessoryFilter = new FoodySearchFilter({
        grid: '#foody-ingredient-feed',
        cols:2,
        page:'body[class~=single-foody_ingredient]',
        context: 'foody_ingredient',
        contextArgs: [
            foodyGlobals.objectID
        ],
    });

    new FoodyContentPaging({
        context: 'foody_ingredient',
        contextArgs: [
            foodyGlobals.objectID
        ],
        filter: accessoryFilter,
        sort:'#sort-foody-ingredient-feed'
    });


});