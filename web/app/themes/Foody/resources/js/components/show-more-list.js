/**
 * Created by moveosoftware on 5/28/18.
 */


/**
 * display overlay on an image
 * in the last element of a list
 * to indicate the count of remaining items
 * */
window.showMoreList = function (selector) {
    let $target = $(selector);

    let count = $target.parent().data('count');
    if (count) {
        let $image = $('img', $target);

        let imageHeight = $image.height();

        let overlay = `<h4 class='show-more-list-overlay'> ${count}+</h4>`;

        $(overlay).css({
            top: 0,
            'line-height': imageHeight + 'px'
        }).appendTo($target.css("position", "relative"));

        $target.addClass('show-more-list');
        $image.css('opacity', '0.5');
    }


};