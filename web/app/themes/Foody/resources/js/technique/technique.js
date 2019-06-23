/**
 * Created by moveosoftware on 11/19/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {

    let techniqueFilter = new FoodySearchFilter({
        grid: '#foody-technique-feed',
        cols:2,
        page:'body[class~=single-foody_technique]',
        context: 'foody_technique',
        contextArgs: [
            foodyGlobals.objectID
        ],
    });

    new FoodyContentPaging({
        context: 'foody_technique',
        contextArgs: [
            foodyGlobals.objectID
        ],
        filter: techniqueFilter,
        sort:'#sort-foody-technique-feed'
    });


});