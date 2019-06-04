/**
 * Created by moveosoftware on 10/9/18.
 */

jQuery(document).ready(($) => {


    let textNormalizer = function (value) {
        return $.trim(value);
    };
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


    $('#register-form .md-checkbox label').on('click', function () {
        let $input = $(this).prev('input[type="checkbox"]');
        let checked = $input.prop('checked');
        $input.prop('checked', checked);
    });

    if ($("#register-form").length) {
        $("#register-form").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    normalizer: textNormalizer
                },
                first_name: {
                    required: true,
                    normalizer: textNormalizer
                },
                last_name: {
                    required: true,
                    normalizer: textNormalizer
                },
                password: {
                    required: true,
                    minlength: 8,
                    password: true,
                    normalizer: textNormalizer
                },
                'password-confirmation': {
                    required: true,
                    equalTo: '#password[name="password"]',
                    normalizer: textNormalizer
                },
                phone_number: {
                    regex: /^((\+972|972)|0)( |-)?([1-468-9]( |-)?\d{7}|(5|7)[0-9]( |-)?\d{7})/
                },
                terms: {
                    required: true
                },
                marketing: {
                    required: '#check-e-book:checked'
                }
            },
            messages: {
                email: 'כתובת המייל אינה תקינה',
                first_name: 'שם פרטי הינו שדה חובה',
                last_name: 'שם משפחה הינו שדה חובה',
                password: 'סיסמא אינה תקינה',
                'password-confirmation': 'סיסמאות אינן תואמות',
                phone_number: 'מספר טלפון נייד אינו תקין',
                terms: 'אנא אשר/י את תנאי השימוש',
                marketing: foodyGlobals.messages.registration.eBookError
            },
            errorPlacement: function (error, element) {
                if (element.attr("type") == "checkbox") {
                    let parent = $(element).parent('.md-checkbox');
                    error.insertBefore(parent);
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                grecaptcha.reset();
                grecaptcha.execute();
            }
        });
    }

    $('.content-with-images .btn-google').on('click', () => {
        $('a[data-provider="Google"]')[0].click();
    });

    $('.content-with-images .btn-facebook').on('click', () => {
        $('a[data-provider="Facebook"]')[0].click();
    });

});