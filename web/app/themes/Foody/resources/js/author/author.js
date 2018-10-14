/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');


jQuery(document).ready(($) => {
    let recipesFilter = new FoodySearchFilter({
        selector: '.author #accordion-foody-filter',
        grid: '#author-recipe-feed',
        cols: 3
    });
    let playlistsFilter = new FoodySearchFilter({
        selector: '.author #accordion-foody-filter',
        grid: '#author-playlist-feed',
        cols: 3
    });

    // // search and filter pager
    let recipesPager = new FoodyContentPaging({
        context: 'author',
        contextArgs: [
            foodyGlobals.objectID,
            'foody_recipe'
        ],
        filter: recipesFilter
    });

    let playlistsPager = new FoodyContentPaging({
        context: 'author',
        contextArgs: [
            foodyGlobals.objectID,
            'foody_playlist'
        ],
        filter: playlistsFilter
    });

});