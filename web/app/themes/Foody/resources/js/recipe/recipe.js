/**
 * Created by moveosoftware on 6/27/18.
 */


window.scroller();

$('.foody-rating').on('rating:change', function (event, value, caption) {
    rating(value);
});


function rating(value) {

    let settings = {
        action: 'foody_rating',
        data: {
            post_id: post.ID,
            post_type: post.type,
            value: value
        }
    };

    foodyAjax(settings, (err) => {

        if (err) {
            // TODO handle
            console.log(err);
        } else {
            console.log('frs');
        }
    });

}
