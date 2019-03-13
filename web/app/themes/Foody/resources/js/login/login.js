/**
 * Created by moveosoftware on 10/10/18.
 */
jQuery(document).ready(($) => {

    let $form = $('#login-form');
    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            return this.optional(element) || regexp.test(value);
        }
    );

    $.validator.addMethod(
        "password",
        function (value) {

            let hasNumbers = /[0-9]+/.test(value);
            let nonEn = /[^a-z0-9]/i.test(value);

            return hasNumbers && nonEn === false;
        }
    );
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