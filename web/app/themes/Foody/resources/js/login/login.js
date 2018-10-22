/**
 * Created by moveosoftware on 10/10/18.
 */
jQuery(document).ready(($) => {

    let $form = $('#login-form');

    $.validator.addMethod(
        "emailOrUsername",
        function (value, element) {
            return this.emal(element) || /^[^a-z0-9\s_.\-@]$/i.test(value);
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



    $('.btn-google').on('click', () => {
        $('a[data-provider="Google"]')[0].click();
    });

    $('.btn-facebook').on('click', () => {
        $('a[data-provider="Facebook"]')[0].click();
    });
});