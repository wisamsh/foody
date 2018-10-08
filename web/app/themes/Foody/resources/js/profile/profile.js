/**
 * Created by moveosoftware on 9/29/18.
 */

let toggleFollowed = require('../common/follow');
let FoodySearchFilter = require('../common/foody-search-filter');

jQuery(document).ready(($) => {

    $('.managed-list li .close').click(function () {

        let $parent = $(this).parent('li');
        let id = $parent.data('id');
        let type = $parent.data('type');


        toggleFollowed(id, type, function (error) {

            if (error) {
                // TODO handle
            } else {


                $parent.fadeOut({
                    duration: 300,
                    complete: function () {
                        $parent.detach();
                    }
                });
            }
        })
    });

    new FoodySearchFilter({grid: '.my-channels-grid',cols:2});
    new FoodySearchFilter({grid: '.my-recipes-grid',cols:2});


});