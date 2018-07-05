/**
 * Created by moveosoftware on 7/3/18.
 */

window.formSubmit = function (settings) {


    let $form = $(settings.form);


    $form.submit(function (e) {

        e.preventDefault();
        let button = $('input[type="submit"]', $form);

        // if comment form isn't in process, submit it
        if (!button.hasClass('loadingform')) {

            // ajax request
            $.ajax({
                type: 'POST',
                url: settings.ajaxUrl, // admin-ajax.php URL
                data: $(this).serialize() + '&action=ajaxcomments', // send form data + action parameter
                beforeSend: function (xhr) {
                    // what to do just after the form has been submitted
                    button.addClass('loadingform').val('Loading...');
                },
                error: function (request, status, error) {
                    if (status == 500) {
                        alert('Error while adding comment');
                    } else if (status == 'timeout') {
                        alert('Error: Server doesn\'t respond.');
                    } else {
                        // process WordPress errors
                        let wpErrorHtml = request.responseText.split("<p>"),
                            wpErrorStr = wpErrorHtml[1].split("</p>");

                        alert(wpErrorStr[0]);
                    }
                },
                success: settings.success,
                complete: function () {
                    // what to do after a comment has been added
                    button.removeClass('loadingform').val('Post Comment');
                }
            });
        }
        return false;
    });


};