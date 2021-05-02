let FoodyLoader = require('../common/foody-loader');

jQuery(document).ready(($) => {
    if (foodyGlobals.post && (foodyGlobals.post.type == 'foody_recipe' || foodyGlobals.post.type == 'post')) {
        if (foodyGlobals['can_user_rate'] ){
            $('.ratings-wrapper .rating-stars-container .empty-star,' +
                '.comments-rating-prep-container .rating .empty-star').on('click', function () {
                    if (foodyGlobals['can_user_rate']) {
                        let parentContainerIsWrapper = $(this).closest('.ratings-wrapper').length;
                        let starIndex = $(this).attr('data-index');
                        let topPracent = parentContainerIsWrapper ? 100 : 8;
                        let container = parentContainerIsWrapper ? '.ratings-wrapper' : '.comments-rating-prep-container .rating';
                        let foodyLoader = new FoodyLoader({
                            container: parentContainerIsWrapper ? $(container)[1] : $(container),
                            id: 'rating-loader'
                        });

                        foodyLoader.attach({topPercentage: topPracent});
                        foodyAjax({
                            action: 'foody_add_rating',
                            data: {
                                postID: foodyGlobals.post.ID,
                                rating: starIndex
                            }
                            }, function (err, data) {
                            if (err) {
                                console.log(err);
                                foodyLoader.detach();
                            } else {
                                foodyLoader.detach();
                                //desktop
                                if ($('.social-and-take-me-container .rating-container .ratings-wrapper').length) {
                                    $('.social-and-take-me-container .rating-container .ratings-wrapper').removeClass('empty');
                                    $('.social-and-take-me-container .rating-container .ratings-wrapper')[0].innerHTML = data.data.details;
                                }

                                //mobile
                                if($('.details.container > .rating-container .ratings-wrapper').length){
                                    $('.details.container > .rating-container .ratings-wrapper').removeClass('empty');
                                    $('.details.container > .rating-container .ratings-wrapper')[0].innerHTML = data.data.details;
                                }

                                if ($('.comments-rating-prep-container .rating').length) {
                                    $('.comments-rating-prep-container .rating')[0].innerHTML = data.data.component;
                                }
                            }
                        });
                    }
                });

            $('.ratings-wrapper .rating-stars-container .empty-star,' +
                '.comments-rating-prep-container .rating .empty-star').on('mouseover', function () {
                toggleRatingIcons(this, true);
            });

            $('.ratings-wrapper .rating-stars-container .empty-star,' +
                '.comments-rating-prep-container .rating .empty-star').on('mouseout', function () {
                toggleRatingIcons(this, false);
            });

            $('.ratings-wrapper .rating-stars-container .full-star,' +
                '.comments-rating-prep-container .rating .full-star').on('mouseover', function () {
                toggleRatingIcons(this, true);
            });

            $('.ratings-wrapper .rating-stars-container .full-star,' +
                '.comments-rating-prep-container .rating .full-star').on('mouseout', function () {
                toggleRatingIcons(this, false);
            });
        }
    }
});

function toggleRatingIcons(currentStar, mouseOver) {
    let currentStarIndex = $(currentStar).attr('data-index');
    const starIcon = mouseOver ? 'icons/rating/rating-full-' : 'icons/rating/rating-empty-', iconSuffix = '.png';
    let otherStars = mouseOver ? $(currentStar).siblings() : $($(currentStar).siblings().get().reverse());

    if(!mouseOver) {
        $(currentStar).attr('src', foodyGlobals.imagesUri + starIcon + currentStarIndex + iconSuffix);
    }

    otherStars.each((index, star) => {
        let starIndex = $(star).attr('data-index');
        if(starIndex < currentStarIndex){
            $(star).attr('src', foodyGlobals.imagesUri + starIcon + starIndex + iconSuffix);
        }
    });

    if(mouseOver) {
        $(currentStar).attr('src', foodyGlobals.imagesUri + starIcon + currentStarIndex + iconSuffix);
    }
}