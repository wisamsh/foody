/**
 * Created by moveosoftware on 10/9/18.
 */

jQuery(document).ready(($) => {


    let textNormalizer = function (value) {
        return $.trim(value);
    };

    $('#course-register-form .checkbox').on('click', function () {
        let $input = $(this).prev('input[type="checkbox"]');
        let checked = $input.prop('checked');
        $input.prop('checked', checked);
    });

    if ($("#course-register-form").length) {
        $("#course-register-form").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    normalizer: textNormalizer
                },
                first_name: {
                    required: true,
                    maxlength: 15,
                    normalizer: textNormalizer
                },
                last_name: {
                    required: true,
                    maxlength: 15,
                    normalizer: textNormalizer
                },
                phone_number: {
                    regex: /^((\+972|972)|0)( |-)?([1-468-9]( |-)?\d{7}|(5|7)[0-9]( |-)?\d{7})/,
                    required: true
                },
                terms: {
                    required: true
                },
            },
            messages: {
                email: 'כתובת המייל אינה תקינה',
                first_name: {
                    required: 'שם פרטי הינו שדה חובה',
                    maxlength: 'שם פרטי יכול להכיל 15 תווים לכל היותר'
                },
                last_name: {
                    required: 'שם משפחה הינו שדה חובה',
                    maxlength: 'שם משפחה יכול להכיל 15 תווים לכל היותר'
                },
                phone_number: {
                    required: 'מספר טלפון הינו שדה חובה',
                    regex: 'מספר טלפון אינו תקין'
                },
                terms: 'אנא אשר/י את תנאי השימוש',
            },
            errorPlacement: function (error, element) {
                if (element.attr("type") == "checkbox") {
                    let parent = $(element).parent('.checkbox');
                    error.insertBefore(parent);
                } else {
                    error.insertAfter(element);
                }
            }
        });

        if($('.credit-card-pay').length){
            $('.credit-card-pay').on('click', function () {
                let email = $('#email').val().length != 0 && !$('#email').hasClass('error') ? $('#email').val() : false;
                let firstName = $('#first-name').val().length != 0 && !$('#first-name').hasClass('error') ? $('#first-name').val() : false;
                let lastName = $('#last-name').val().length != 0 && !$('#last-name').hasClass('error') ? $('#last-name').val() : false;
                let phone = $('#phone-number').val().length != 0 && !$('#phone-number').hasClass('error') ? $('#phone-number').val() : false;

                if(email && firstName && lastName && phone){
                    let link  = $(this).attr('data-link') + '?ExtCUserEmail=' + email + '&ExtCInvoiceTo=' + 'לכבוד ' + firstName + ' ' + lastName + '&ExtMobilPhone=' + phone;
                    let iframe = '<iframe src="'+ link +'" style="width: 100%;\n' +
                    'height: auto;\n' +
                    'min-height: 1200px;\n' +
                    'padding-top: 3%;\n' +
                    'border: none;"></iframe>';

                    $('.bottom-image').remove();
                    $('.form-section').replaceWith(iframe);
                }
                else{
                    let fields = { '#email': email ,'#first-name' : firstName,'#last-name' : lastName, '#phone-number' : phone };
                   for(let field in fields){
                        if(!fields[field]){
                            $(field).attr('style', 'border-color: red');
                        }
                        else {
                            $(field).attr('style', 'border-color: #ccc');
                        }
                    }
                }
            });
        }
    }
});