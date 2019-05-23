/**
 * Created by moveosoftware on 10/8/18.
 */

jQuery(document).ready(($) => {

    let $slider = $('.brands-slider');

    if ($slider.length) {
        // options loaded as data attribute.
        // see template-parts/white-label/content-brands-slider.php
        $slider.slick();
    }
    if (!foodyGlobals.isMobile){
        $(`.slick-slide[data-slick-index=1]`).addClass('item-border');
        $slider.on('afterChange', function (event, slick, currentSlide, nextSlide) {

            let indices = $('.slick-active').map((i, el) => {
                return $(el).data('slick-index');
            });

            $(`.slick-slide[data-slick-index=${indices[1]}]`).addClass('item-border');
            $(`.slick-slide[data-slick-index=${indices[0]}]`).removeClass('item-border');
            $(`.slick-slide[data-slick-index=${indices[2]}]`).removeClass('item-border');

        });
    }

});
