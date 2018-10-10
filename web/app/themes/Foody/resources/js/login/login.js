/**
 * Created by moveosoftware on 10/10/18.
 */
jQuery(document).ready(($) => {

    let $form = $('#login-form');

    $form.validate({
        rules: {
            log: {
                required: true,
                email: true
            },
            pwd: {
                required: true
            }
        },
        messages: {
            log: 'כתובת המייל אינה תקינה',
            pwd: 'סיסמא אינה תקינה',
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});