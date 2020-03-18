jQuery(document).ready(($) => {


    $(document).on('click', '.chapter-container,.faq-item', function () {
        let $bullets = $(this);
        $bullets.toggleClass('show-details');
    });

    $('.testimonials-slider').slick({
            slidesToShow: 3,
            rtl: true,
            prevArrow: '<img class="arrow arrow-prev" src="' + foodyGlobals.imagesUri + 'icons/arrow-right.svg" />',
            nextArrow: '<img class="arrow arrow-next" src="' + foodyGlobals.imagesUri + 'icons/arrow-left.svg" />',
            responsive: [
                {
                    breakpoint: 420,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
            ]
        }
    )


});