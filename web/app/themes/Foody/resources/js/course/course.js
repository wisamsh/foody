/**
 * Created by omerfishman on 14/7/19.
 */
jQuery(document).ready(($) => {

    let $stickyRegistration = $('.foody_course-template-page-templates .sticky-registration');

    let recommenders_slide = $('.foody_course-template-page-templates .recommendations-container');
    if (recommenders_slide.length) {
        // options loaded as data attribute.
        // see page-templates/foody-course.php
        recommenders_slide.slick();
    }

    // fades the floating registration link in/out
    // and hides/shows based on scroll position to make sure
    // header isn't blocked
    $(window).scroll(function () {
        let threshold = 400; // number of pixels before top of page that you want to start fading in
        let op = ($(window).scrollTop()) / threshold;
        let pointerEvents = "all";

        if (op <= 0.2) {
            op = 0;
            pointerEvents = "none"
        }
        $stickyRegistration.css("opacity", op);
        $stickyRegistration.css("pointer-events", pointerEvents);
    });
});