/**
 * Created by moveosoftware on 10/10/18.
 */
jQuery(document).ready(($) => {

    let $form = $('#login-form');
    if ($.validator) {
        $.validator.addMethod(
            "regex",
            function (value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }
        );
    }

    if ($.validator) {
        $.validator.addMethod(
            "password",
            function (value) {

                let hasNumbers = /[0-9]+/.test(value);
                let nonEn = /[^a-z0-9]/i.test(value);

                return hasNumbers && nonEn === false;
            }
        );
    }

    if ($.validator) {
        $.validator.addMethod(
            "emailOrUsername",
            function (value, element) {
                return this.email(element) || /^[^a-z0-9\s_.\-@]$/i.test(value);
            }
        );
    }

    if ($form && $form.validate) {
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
    }


    $('.login .btn-google').on('click', () => {
        $('a[data-provider="Google"]')[0].click();
    });

    $('.login .btn-facebook').on('click', () => {
        $('a[data-provider="Facebook"]')[0].click();
    });
});