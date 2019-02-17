/**
 * Created by moveosoftware on 6/20/18.
 */

jQuery(document).ready(function () {
    $('#team-sort').on('changed.bs.select', function () {


        let $parent = $('.team-listing');
        let parents = $('.team-grid-row', $parent);
        let creators = $('.author', $parent).toArray();

        let newOrder;
        let newValue = $(this).val();

        console.log('change', newValue);
        console.log(creators);
        if (newValue) {
            newOrder = _.sortBy(creators, function (creator) {
                console.log(creator);
                console.log($('> div', creator).attr('data-name'));
                return $('> div', creator).attr('data-name');
            });

            if (newValue == -1) {
                newOrder = newOrder.reverse();
            }
        } else {
            newOrder = _.sortBy(creators, function (creator) {
                return parseInt($('> div', creator).attr('data-order'));
            });
        }

        let itemsPerRow = $('.author', parents[0]).length;

        let emptyAuthor = $('.authorempty')[0];

        let rows = [];

        newOrder.forEach(function (author, i) {
            let rowIndex = parseInt(i / itemsPerRow);
            rows[rowIndex] = rows[rowIndex] || [];
            rows[rowIndex].push(author);
        });

        rows = rows.map(function (rowAuthors) {
            let wrapper = $(parents[0]).clone().empty();
            $(wrapper).append(rowAuthors);
            return $(wrapper);
        });


        while (rows[rows.length - 1].children().length < itemsPerRow) {
            rows[rows.length - 1].append($(emptyAuthor).clone());
        }

        $parent.fadeOut(200, function () {
            $(this).delay(200).empty().append(rows).fadeIn();
        });


    });
});