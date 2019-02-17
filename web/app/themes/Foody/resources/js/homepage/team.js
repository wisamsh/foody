/**
 * Created by moveosoftware on 5/15/18.
 */


jQuery(document).ready(($) => {
    showMoreList('.homepage .team-listing .author:last-child', 'הנבחרת');
    // if (foodyGlobals.isMobile) {
    // showSlider();
    // }
});


function showSlider() {
    let teamSliderSelector = '.homepage .team-listing';
    if (foodyGlobals.isMobile) {
        $('.homepage .team-listing .author').removeClass('col');
    }
    slider(teamSliderSelector, {
        slidesToShow: 5.5,
        slidesToScroll: 5,
        arrows: false,
        slideSpacing: 10,
        mobileFirst: true,
        responsive: [
            {
                breakpoint: 1025,
                settings: 'unslick'
            },
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 7,
                    variableWidth: false,
                }
            },
            {
                breakpoint: 415,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 6,
                    variableWidth: false,
                }
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
}