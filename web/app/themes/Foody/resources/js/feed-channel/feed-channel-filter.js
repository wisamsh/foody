let FoodySearchFilter = require('../feed-channel/feed-channel-search-filter');
let FoodyContentPaging = require('../feed-channel/feed-channel-content-paging');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({
        grid: $("body.foody_feed_channel-template .content .foody-grid .row"),
        cols: 2,
        page: 'body[class~=foody_feed_channel-template]',
        context: 'foody_filter',
        contextArgs: [
            foodyGlobals.objectID
        ]
    });

    new FoodyContentPaging({
        context: 'area',
        filter: filter,
        sort: '#sort-foody-filter-feed',
        contextArgs: [
            foodyGlobals.objectID
        ]
    });

});