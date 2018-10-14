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


        let eventName = null;

        if (type == 'followed_channels') {
            eventName = 'remove channel';
        } else if (type == 'followed_authors') {
            eventName = 'remove creator';
        }

        if (eventName) {
            analytics.event(eventName,{
                id:id,
                title:$('a',$parent).text()
            });
        }


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

    new FoodySearchFilter({
        selector: '.page-template-profile #accordion-foody-filter',
        grid: '.my-channels-grid',
        cols: 2
    });
    new FoodySearchFilter({
        selector: '.page-template-profile #accordion-foody-filter',
        grid: '.my-recipes-grid',
        cols: 2
    });


    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

        console.log(e.target);

        let tab = $(e.target).attr('href');

        tab = tab.replace('#', '');

        let eventName = null;
        if (tab == 'my-channels-recipes') {
            eventName = 'my channel recipes';
        } else if (tab == 'my-recipes') {
            eventName = 'my recipes button';
        }

        if (eventName != null) {

            analytics.event(eventName);
        }

    })


});