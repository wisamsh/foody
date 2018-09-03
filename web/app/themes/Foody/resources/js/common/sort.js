/**
 * Created by moveosoftware on 9/3/18.
 */

module.exports = function (selector,itemSelector) {
    $('.foody-sort').on('changed.bs.select', function () {


        let $parent = $(selector);
        let items = $(itemSelector, $parent).toArray();

        let newOrder;
        let newValue = $(this).val();

        if (newValue) {
            newOrder = _.sortBy(items, function (item) {
                return $(item).data('sort');
            });

            if (newValue == -1) {
                newOrder = newOrder.reverse();
            }
        } else {
            newOrder = _.sortBy(items, function (item) {
                return $(item).data('order');
            });
        }

        $parent.fadeOut(200, function () {
            $(this).delay(200).empty().append(newOrder).fadeIn();
        });
    });
};