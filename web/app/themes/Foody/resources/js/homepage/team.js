/**
 * Created by moveosoftware on 5/15/18.
 */


jQuery(document).ready(($) => {
    if (!foodyGlobals.isMobile) {
        showMoreList('.homepage .team-listing .author:last-child');
    } else {
        showSlider()
    }
});


function showSlider() {
    let teamSliderSelector = '.homepage .team-listing';
    $('.homepage .team-listing .author').removeClass('col');
    slider(teamSliderSelector, {
        slidesToShow: 1,
        rtl: true,
        variableWidth: true,
        arrows: false,
        slideSpacing: 10,
        responsive: [
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    variableWidth: false,
                }
            },
            {
                breakpoint: 414,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll: 3,
                    variableWidth: false,
                }
            },
            {
                // breakpoint: 1440,
                // settings: 'unslick'
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
}