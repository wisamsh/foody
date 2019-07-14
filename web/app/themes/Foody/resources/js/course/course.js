/**
 * Created by omerfishman on 14/7/19.
 */
jQuery(document).ready(($) => {

    let recommenders_slide = $('.recommendations-container');
    if (recommenders_slide.length) {
        // options loaded as data attribute.
        // see page-templates/foody-course.php
        recommenders_slide.slick();
    }
});