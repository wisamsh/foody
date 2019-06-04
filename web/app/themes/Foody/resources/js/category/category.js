/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = import('../common/foody-search-filter');
let FoodyContentPaging = import('../common/page-content-paging');

jQuery(document).ready(($) => {

    let filter = new FoodySearchFilter({
        grid: '#category-feed',
        cols: 2,
        page: 'body[class~=category]',
        context: 'category',
        contextArgs: [
            foodyGlobals.objectID
        ],
    });

    new FoodyContentPaging({
        context: 'category',
        contextArgs: [
            foodyGlobals.objectID
        ],
        filter: filter,
        sort: '#sort-category-feed'
    });

    let $slider = $('.categories-slider');

    if ($slider.length) {
        // options loaded as data attribute.
        // see template-parts/content-category-slider.php
        $slider.slick();
    }


});