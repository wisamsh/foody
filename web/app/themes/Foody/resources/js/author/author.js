/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');


jQuery(document).ready(($) => {
    let recipesFilter = new FoodySearchFilter({
        selector: '.author #accordion-foody-filter',
        grid: '#author-recipe-feed',
        cols: 2,
        page:'body[class~=author]',
        context: 'author',
        contextArgs: [
            foodyGlobals.objectID,
            'foody_recipe'
        ],
    });

    let playlistsFilter = new FoodySearchFilter({
        selector: '.author #accordion-foody-filter',
        grid: '#author-playlist-feed',
        cols: 2,
        page:'body[class~=author]',
        context: 'author',
        contextArgs: [
            foodyGlobals.objectID,
            'foody_playlist'
        ],
    });

    // // search and filter pager
    let recipesPager = new FoodyContentPaging({
        context: 'author',
        contextArgs: [
            foodyGlobals.objectID,
            'foody_recipe'
        ],
        filter: recipesFilter,
        sort: '#sort-author-recipe-feed'
    });

    let playlistsPager = new FoodyContentPaging({
        context: 'author',
        contextArgs: [
            foodyGlobals.objectID,
            'foody_playlist'
        ],
        filter: playlistsFilter,
        sort: '#sort-author-playlist-feed'
    });

});