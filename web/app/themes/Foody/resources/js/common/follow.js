/**
 * Created by moveosoftware on 7/22/18.
 */


// $(document).ready(() => {

let $follow = $('.btn-follow');

if ($follow.length) {


    $follow.each(function () {


        let $this = $(this);

        $this.click(() => {
            let topicId = $this.data('id');
            let topic = $this.data('topic');


            if (topicId) {

                let isAlreadyFollowed = $this.data('followed');

                toggleAllFollowed(topicId, isAlreadyFollowed);


                $.ajax({
                    type: 'POST',
                    url: '/wp/wp-admin/admin-ajax.php', // admin-ajax.php URL
                    data: {
                        action: 'toggle_follow',
                        topic_id: topicId,
                        topic: topic
                    },
                    error: function (request, status, error) {

                        // TODO handle errors

                        if (status == 500) {
                            alert('Error while adding comment');
                        } else if (status == 'timeout') {
                            alert('Error: Server doesn\'t respond.');
                        } else {
                            alert('error: ' + status);
                        }
                        // revert animations and favorite indication
                        toggleAllFollowed(topicId, !isAlreadyFollowed);
                    },
                    success: () => {
                    },
                    complete: function () {
                    }
                })

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


// });