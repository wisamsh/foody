/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

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

    let categoryPager = new FoodyContentPaging({
        context: 'category',
        contextArgs: [
            foodyGlobals.objectID
        ],
        filter: filter,
        sort: '#sort-category-feed'
    });

    let $slider = $('.categories-slider');

    if ($slider.length) {
        let options = {
            "slidesToShow": 4,
            "rtl": true,
            "prevArrow": "<i class=\"icon-arrowleft prev\"></i>",
            "nextArrow": "<i class=\"icon-arrowleft next\"></i>",
            "slidesToScroll": 3,
            "infinite": false,
            "responsive": [{
                "breakpoint": 1441,
                "settings": {"slidesToShow": 5, "arrows": false, "slidesToScroll": 3}
            }, {
                "breakpoint": 1025,
                "settings": {"slidesToShow": 5, "arrows": false, "slidesToScroll": 3}
            }, {"breakpoint": 415, "settings": {"slidesToShow": 3, "arrows": false, "slidesToScroll": 3}}]
        };

        // $slider.slick(options);
    }


});