/**
 * Created by moveosoftware on 10/10/18.
 */
jQuery(document).ready(($) => {

    let $form = $('#login-form');

    console.log('laskhflkashf');

    $.validator.addMethod(
        "emailOrUsername",
        function (value, element) {
            return this.email(element) || /^[^a-z0-9\s_.\-@]$/i.test(value);
        }
    );

    $form.validate({
        rules: {
            log: {
                required: true,
                emailOrUsername: true
            },
            pwd: {
                required: true
            }
        },
        messages: {
            log: 'כתובת המייל/שם המשתמש אינה תקינה',
            pwd: 'סיסמא אינה תקינה',
        },
        submitHandler: function (form) {
            form.submit();
        }
    });


    $('.content-with-images .btn-google').on('click', () => {
        $('a[data-provider="Google"]')[0].click();
    });

    $('.content-with-images .btn-facebook').on('click', () => {
        $('a[data-provider="Facebook"]')[0].click();
    });
});