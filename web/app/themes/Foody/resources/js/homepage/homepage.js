/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');
let FoodyLocationUtils = require('../common/foody-location-utils');
let FoodyLoader = require('../common/foody-loader');

let locationUtils = new FoodyLocationUtils();


jQuery(document).ready(($) => {


    if(!foodyGlobals.isMobile){
        foodyAjax({action: 'load_foody_social'}, (err, data) => {
            $('aside.sidebar-desktop  .sidebar-content').append(data);
        });
    }

    let feedContainer = $('.page-template-homepage .feed-container .content-container');
    let loader = new FoodyLoader({container:feedContainer});
    loader.attach();
    foodyAjax({action: 'load_homepage_feed',data:{filter:locationUtils.getQuery('filter')}}, (err, data) => {
        loader.detach();
        $(feedContainer).append(data);
        $('#sort-homepage-feed').selectpicker({dropdownAlignRight: true, style: 'foody-select', dropupAuto: false, width: 'fit'});

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
    })
});