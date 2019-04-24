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


});
