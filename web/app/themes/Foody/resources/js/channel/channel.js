/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {
    let recipesFilter = new FoodySearchFilter({
        selector: '.foody_channel-template #accordion-foody-filter',
        grid: '#channel-recipe-feed',
        cols: 3
    });
    let playlistsFilter = new FoodySearchFilter({
        selector: '.foody_channel-template #accordion-foody-filter',
        grid: '#channel-playlist-feed',
        cols: 3
    });

    // // search and filter pager
    // let recipesPager = new FoodyContentPaging({
    //     context: 'channel',
    //     contextArgs: [
    //         foodyGlobals.objectID,
    //         'foody_recipe'
    //     ],
    //     filter: recipesFilter
    // });
    //
    // let playlistsPager = new FoodyContentPaging({
    //     context: 'channel',
    //     contextArgs: [
    //         foodyGlobals.objectID,
    //         'foody_playlist'
    //     ],
    //     filter: playlistsFilter
    // });
});