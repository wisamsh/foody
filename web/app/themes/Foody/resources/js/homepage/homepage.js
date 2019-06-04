/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = import('../common/foody-search-filter');
let FoodyContentPaging = import('../common/page-content-paging');

jQuery(document).ready(($) => {
    // sidebar filter
    let filter = new FoodySearchFilter({
        selector: '.homepage #accordion-foody-filter',
        grid: '#homepage-feed',
        cols: 2,
        searchButton: '.show-recipes',
        page: '.page-template-homepage',
        context: 'homepage',
        contextArgs: [],
    });

    // search and filter pager
    let pager = new FoodyContentPaging({
        context: 'homepage',
        contextArgs: [],
        filter: filter,
        sort: '#sort-homepage-feed'
    });
});