/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');

jQuery(document).ready(($) => {



    // load_homepage_content
    let ajaxSettings = {
        action: 'load_homepage_content',
        data: {}
    };

    foodyAjax(ajaxSettings, function (err, data) {

        if (!err) {
            $('#homepage-main-content').append(data);
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
        }
    });
});