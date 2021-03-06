/**
 * Created by moveosoftware on 5/28/18.
 */


/**
 * display overlay on an image
 * in the last element of a list
 * to indicate the count of remaining items
 * */
window.showMoreList = function (selector, moreLink) {
    let $target = $(selector);

    if ($target && $target.length) {
        let count = $target.parent().data('count');
        if(!moreLink){
            moreLink = $target.parent().data('more-link');
        }
        let $image = $('img', $target);
        $('h2', $target).text('הצג הכל');

        let $imageContainer = $('.image-container', $target);
        let overlay = `<span class='show-more-list-overlay'> ${count}+</span>`;

        $imageContainer.css("position", "relative");
        $(overlay).appendTo($imageContainer);


        $target.addClass('show-more-list');

        let $link = $image.closest('a');

        $link.attr('href', moreLink);

        $image.css('opacity', '0.2');

        $target.parent().addClass('showing-more')
    }


};