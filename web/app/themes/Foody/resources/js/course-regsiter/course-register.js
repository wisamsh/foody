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

        if ($('.credit-card-pay').length) {
            $('.credit-card-pay').on('click', function () {
                let email = $('#email').val().length != 0 && !$('#email').hasClass('error') ? $('#email').val() : false;
                let firstName = $('#first-name').val().length != 0 && !$('#first-name').hasClass('error') ? $('#first-name').val() : false;
                let lastName = $('#last-name').val().length != 0 && !$('#last-name').hasClass('error') ? $('#last-name').val() : false;
                let phone = $('#phone-number').val().length != 0 && !$('#phone-number').hasClass('error') ? $('#phone-number').val() : false;

                let termsAccepted = $('.newsletter-and-terms #terms').prop('checked');
                if (termsAccepted && email && firstName && lastName && phone) {
                    let mailInvoice = $(this).attr('data-invoice-mail').length != 0 ? $(this).attr('data-invoice-mail') : '';
                    let mailNotice = mailInvoice != '' ? '<span class="invoice-notice">*במידה ותרצה לשנות את שם החשבונית יש ליצור קשר במייל ' + '<a href="mailto:'+ mailInvoice +'">'+ mailInvoice +'</a></span>' : '';
                    let thankYou = $(this).attr('data-thank-you').length != 0 ? $(this).attr('data-thank-you') : '';
                    let enableMarketing = $('.newsletter-and-terms #newsletter').prop('checked') ? 'מאשר קבלת דואר' : 'לא מאשר קבלת דואר';
                    // let price = $(this).data('item-price');
                    // let itemName = $(this).data('item-name');
                    let link = $(this).attr('data-link') +  '?ExtCUserEmail=' + email + '&ExtCInvoiceTo=' + firstName + ' ' + lastName + '&ExtMobilPhone=' + phone + '&SuccessRedirectUrl=' + thankYou + '&custom_field_10=' + enableMarketing;

                    let iframe = '<iframe id="card-pay-frame" src="' + link + '" style="width: 100%;\n' +
                        'height: auto;\n' +
                        'min-height: 1500px;\n' +
                        'padding-top: 3%;\n' +
                        'border: none;" scrolling="no"></iframe>';


                    // foodyAjax({
                    //     action: 'foody_sign_to_newsletter_by_email',
                    //     data: {
                    //         marketing: $('.newsletter-and-terms #newsletter').prop('checked'),
                    //         email: email
                    //     }
                    // }, function () {});

                    $('.cover-section').remove();
                    $('.bottom-image').remove();
                    $('.form-section').replaceWith(iframe);
                    $('#card-pay-frame').after(mailNotice);
                } else {
                    let fields = {
                        '#email': email,
                        '#first-name': firstName,
                        '#last-name': lastName,
                        '#phone-number': phone,
                        '#terms' : termsAccepted
                    };
                    for (let field in fields) {
                        if (!fields[field]) {
                            if(field == '#terms'){
                                let errorMsg = '<span class="terms-error">' + '*אנא אשר/י את תנאי השימוש' + '</span>';
                                if($('.terms-error').length == 0 ) {
                                    $('.newsletter-and-terms').after(errorMsg);
                                }
                            }
                            else {
                                $("#course-register-form").validate().element(field);
                            }
                        } else {
                            $('.terms-error').remove();
                        }
                    }
                }
            });
            $('#card-pay-frame').load(function () {
                this.attr('src', link);
            })

        }
        // $('#course-register-form input').focusout(function () {
        //     if(!$(this).hasClass('form-checkbox')){
        //         if($(this).hasClass('foody-input-error') && $(this).val().length){
        //             $(this).removeClass('foody-input-error');
        //             $(this).attr('style', 'border-color: #ccc')
        //         }
        //     }
        // });

        // $('.thank-you-text').on('load', function () {
        //     $('.invoice-notice').remove();
        // });

        // $('#card-pay-frame').on('load',function () {
        //     debugger;
        //     let iFrameID = $('#card-pay-frame');
        //     if(iFrameID.length) {
        //         // here you can make the height, I delete it first, then I make it again
        //         iFrameID.height = "";
        //         iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
        //     }
        // });
    }
});
