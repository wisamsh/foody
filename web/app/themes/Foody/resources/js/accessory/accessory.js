/**
 * Created by moveosoftware on 11/19/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let accessoryFilter = new FoodySearchFilter({
        grid: '#foody-accessory-feed',
        cols:2,
        page:'body[class~=single-foody_accessory]',
        context: 'foody_accessory',
        contextArgs: [
            foodyGlobals.objectID
        ],
    });

    new FoodyContentPaging({
        context: 'foody_accessory',
        contextArgs: [
            foodyGlobals.objectID
        ],
        filter: accessoryFilter,
        sort:'#sort-foody-accessory-feed'
    });


});