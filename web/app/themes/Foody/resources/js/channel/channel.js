/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = import('../common/foody-search-filter');
let FoodyContentPaging = import('../common/page-content-paging');

jQuery(document).ready(($) => {
    let recipesFilter = new FoodySearchFilter({
        selector: '.foody_channel-template #accordion-foody-filter',
        grid: '#channel-recipe-feed',
        cols: 2,
        page: '.foody_channel-template',
        context: 'channel',
        contextArgs: [
            foodyGlobals.objectID
        ]
    });
    // let playlistsFilter = new FoodySearchFilter({
    //     selector: '.foody_channel-template #accordion-foody-filter',
    //     grid: '#channel-playlist-feed',
    //     cols: 2,
    //     page: '.foody_channel-template',
    //     context: 'channel',
    //     contextArgs: [
    //         foodyGlobals.objectID
    //     ]
    // });

    // search and filter pager
    let recipesPager = new FoodyContentPaging({
        context: 'channel',
        contextArgs: [
            foodyGlobals.objectID,
            'foody_recipe'
        ],
        filter: recipesFilter,
        sort: '#channel-recipe-feed'
    });
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