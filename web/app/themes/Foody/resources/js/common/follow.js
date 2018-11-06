/**
 * Created by moveosoftware on 7/22/18.
 */


$(document).ready(() => {

    let $follow = $('.btn-follow');

    if ($follow.length) {


        $follow.each(function () {


            let $this = $(this);

            $this.click(() => {
                if (foodyGlobals.loggedIn == 'false') {
                    return showLoginModal();
                }
                let topicId = $this.data('id');
                let topic = $this.data('topic');


                if (topicId) {

                    let isAlreadyFollowed = $this.data('followed');

                    let eventName = null;
                    if (topic == 'followed_channels') {
                        eventName = 'follow channel';
                    } else if (topic == 'followed_authors') {
                        eventName = 'follow creator';
                    }

                    if (eventName != null) {
                        analytics.event(eventName, {
                            id: topicId,
                            title: $('h1', $this.closest('.topic-details')).text(),
                            follow: !isAlreadyFollowed
                        });
                    }

                    toggleAllFollowed(topicId, isAlreadyFollowed);


                    toggleFollowed(topicId, topic, function (error) {

                        if (error) {
                            if (error.status == 500) {
                                console.log('Error while adding comment');
                            } else if (error.status == 'timeout') {
                                console.log('Error: Server doesn\'t respond.');
                            } else {
                                showLoginModal();
                            }
                            // revert animations and favorite indication
                            toggleAllFollowed(topicId, !isAlreadyFollowed);
                        }
                    });
                }
            });
        });
    }


    function toggleAllFollowed(topicId, isAlreadyFollowed) {
        isAlreadyFollowed = !isAlreadyFollowed;
        $('.btn-follow[data-id="' + topicId + '"]').each(function () {
            $(this).toggleClass('followed');
            $(this).data('followed', isAlreadyFollowed);
            if (isAlreadyFollowed) {
                $('span', this).text('עוקב');
            } else {
                $('span', this).text('עקוב');
            }
        });
    }


});

function toggleFollowed(topicId, topic, cb) {
    $.ajax({
        type: 'POST',
        url: '/wp/wp-admin/admin-ajax.php', // admin-ajax.php URL
        data: {
            action: 'toggle_follow',
            topic_id: topicId,
            topic: topic
        },
        error: function (request, status, error) {

            cb({error: error, status: status});
        },
        success: () => {
        },
        complete: function () {
            cb();
        }
    })
}

module.exports = toggleFollowed;