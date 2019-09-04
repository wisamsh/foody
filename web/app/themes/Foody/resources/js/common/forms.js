/**
 * Created by moveosoftware on 7/3/18.
 */

window.formSubmit = function (settings) {


    let $form = $(settings.form);
    let action = settings.action;

    function handler(e) {
        if (!$form.valid()) {
            return;
        }
        e.preventDefault();
        let button = $('input[type="submit"]', $form);
        if (button.length == 0) {
            button = $('button[type="submit"]', $form);
        }

        // if comment form isn't in process, submit it
        if (!button.hasClass('loadingform')) {


            let ajaxSettings = {
                type: 'POST',
                url: settings.ajaxUrl, // admin-ajax.php URL
                data: $(this).serialize() + action, // send form data + action parameter
            };

            if (settings.ajaxSettings) {
                for (let key in settings.ajaxSettings) {
                    if (settings.ajaxSettings.hasOwnProperty(key)) {
                        ajaxSettings[key] = settings.ajaxSettings[key];
                    }
                }
            }

            // ajax request
            $.ajax(ajaxSettings);
        }
        return false;
    }

    if (!settings.unbind) {
        $form.on('submit', handler);
    }

    return handler;
};


window.formSubmitWithFiles = function (settings) {

    let $form = $(settings.form);
    let action = settings.action;

    $form.submit(function (e) {

        e.preventDefault();
        if (settings.onSubmit && typeof  settings.onSubmit == 'function') {
            settings.onSubmit();
        }
        let button = $('input[type="submit"]', $form);
        if (button.length == 0) {
            button = $('button[type="submit"]', $form);
        }

        // if comment form isn't in process, submit it
        if (!button.hasClass('loadingform')) {

            let data = new FormData();
            data.append('action', action);

            $.each($(this).serializeArray(), function (_, kv) {
                data.append(kv.name, kv.value)
            });

            $.each($('input[type="file"]', this), function () {
                data.append($(this).attr('name'), $(this)[0].files[0])
            });


            // ajax request
            $.ajax({
                type: 'POST',
                url: settings.ajaxUrl, // admin-ajax.php URL
                data: data, // send form data + action parameter
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    // what to do just after the form has been submitted
                    button.addClass('loadingform').prop('disabled', true).val('Loading...');
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
                    button.removeClass('loadingform').prop('disabled', false).val('שלח');

                    if (settings.complete && typeof  settings.complete == 'function') {
                        settings.complete();
                    }
                }
            });
        }
        return false;
    });
};


jQuery(document).ready(($) => {


    let $checkboxes = $('.md-checkbox');

    if ($checkboxes.length) {
        $('body').on('click','.md-checkbox', function (e) {
            if (e.target && !e.target.type && e.target.tagName == "A") {
                console.log('');
            } else {
                e.stopPropagation();
                e.preventDefault();
            }
            if ($(this).attr('disabled')) {
                e.preventDefault();
                return;
            }
            let $input = $('input', this);

            let checked = $input.prop('checked') || false;

            checked = !checked;

            $input.prop('checked', checked);
            $input.trigger("change");
        });
    }
});