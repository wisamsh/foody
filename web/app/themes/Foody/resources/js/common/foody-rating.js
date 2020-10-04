let FoodyLoader = require('../common/foody-loader');

jQuery(document).ready(($) => {
    if (foodyGlobals.post && (foodyGlobals.post.type == 'foody_recipe' || foodyGlobals.post.type == 'post')) {
        if (foodyGlobals['can_user_rate'] &&
            ($('.ratings-wrapper .rating-stars-container .empty-star').length ||
                $('.comments-rating-prep-container .rating .empty-star').length )){
            $('.ratings-wrapper .rating-stars-container .empty-star,' +
                '.comments-rating-prep-container .rating .empty-star').on('click', function () {
                    let parentContainerIsWrapper = $(this).closest('.ratings-wrapper').length;
                let starIndex = $(this).attr('data-index');
                let topPracent = parentContainerIsWrapper ? 27 : 15;
                let container = parentContainerIsWrapper ? '.ratings-wrapper' : '.comments-rating-prep-container .rating';
                let foodyLoader = new FoodyLoader({
                    container: $(container),
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
                        if ($('.ratings-wrapper').length) {
                            $('.ratings-wrapper').removeClass('empty');
                            $('.ratings-wrapper')[0].innerHTML = data.data.details;
                        }

                        if ($('.comments-rating-prep-container .rating').length) {
                            $('.comments-rating-prep-container .rating')[0].innerHTML = data.data.component;
                        }
                    }
                });
            });
        }
    }
});