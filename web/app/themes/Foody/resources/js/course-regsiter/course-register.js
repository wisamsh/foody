/**
 * Created by moveosoftware on 10/9/18.
 */
let FoodyLoader = require('../common/foody-loader');
let price;
let used_coupon_details = null;
let startedBitPayment = false;
jQuery(document).ready(($) => {
    foodyAjax({
        action: 'foody_get_course_price',
        data: {
            course_id: $('.course-information').attr('data-course-id'),
        }
    }, function (err, data) {
        if (err) {
            console.log(err)
        } else {
            if (data.data.course_price) {
                price = data.data.course_price;
                // let price = getCoursePrice();
                let buttonHeight;
                let form_fields = [
                    '#email',
                    '#first-name',
                    '#last-name',
                    '#phone-number',
                    '#terms',
                    '#newsletter',
                    '#coupon-input',
                    '#redeem-coupon'
                ];


                if ($(window).width() < 768) {
                    buttonHeight = 59;
                } else {
                    buttonHeight = 52;
                }

                let textNormalizer = function (value) {
                    return $.trim(value);
                };

                $('#redeem-coupon').on('click', function () {
                    redeemCoupon();
                });

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

                    if ($('.bit-pay').length) {
                        $('.bit-pay').on('click', function () {
                            let email = $('#email').val().length != 0 && !$('#email').hasClass('error') ? $('#email').val() : false;
                            let firstName = $('#first-name').val().length != 0 && !$('#first-name').hasClass('error') ? $('#first-name').val() : false;
                            let lastName = $('#last-name').val().length != 0 && !$('#last-name').hasClass('error') ? $('#last-name').val() : false;
                            let phone = $('#phone-number').val().length != 0 && !$('#phone-number').hasClass('error') ? removeSpacesAndDashFromPhone($('#phone-number').val()) : false;
                            let enableMarketing = $('.newsletter-and-terms #newsletter').prop('checked') ? true : false;
                            let courseName = $(this).attr('data-item-name').length != 0 ? $(this).attr('data-item-name') : false;


                            let termsAccepted = $('.newsletter-and-terms #terms').prop('checked');
                            if (termsAccepted && email && firstName && lastName && phone && courseName) {
                                // temp => only send data to members plugin
                                let couponAndPriceObj = checkCouponAndGetCouponAndPrice(used_coupon_details, price);
                                let foodyLoader = new FoodyLoader({container: $('#course-register-form')});

                                // todo: load bit pay button
                                $.each(form_fields, function (index, value) {
                                    $(value).attr('disabled', true);
                                    if (value == '#redeem-coupon') {
                                        $(value).attr('style', 'cursor: not-allowed');
                                    }
                                });
                                $(this).off('click');

                                //load bit button
                                let bitTransactionId = null;
                                let bitPaymentInitiationId = null;
                                let bitTableId = null;
                                let data_of_member = {
                                    'email': email,
                                    'first_name': firstName,
                                    'last_name': lastName,
                                    'phone': phone,
                                    'purchase_date': get_current_date(),
                                    'enable_marketing': enableMarketing,
                                    'course_name': courseName,
                                    'price': couponAndPriceObj.price,
                                    'payment_method': 'ביט',
                                    'transaction_id': '-1',
                                    'coupon': couponAndPriceObj.coupon,
                                    'status': 'pending',
                                    'payment_method_id': '-1'
                                };
                                foodyLoader.attach();
                                foodyAjax({
                                        action: 'foody_start_bit_pay_process',
                                        data: {
                                            email: email,
                                            first_name: firstName,
                                            last_name: lastName,
                                            price: couponAndPriceObj.price,
                                            item_name: courseName,
                                            memberData: data_of_member
                                        }
                                    }, function (err, data) {
                                        if (err) {
                                            console.log(err);
                                            foodyLoader.detach();
                                        } else {
                                            if (data.data.single_payment_ids) {
                                                bitTransactionId = data.data.single_payment_ids['transactionSerialId'];
                                                bitPaymentInitiationId = data.data.single_payment_ids['paymentInitiationId'];
                                                bitTableId = data.data.single_payment_ids['paymentMethodId'];

                                                $('.button-container').after('<div class="bit-button-container"><span class="bit-btn-text">לחץ כאן להשלמת תשלום בביט</span><span class="bit-notice-text">*חשוב לא לסגור את העמוד עד סיום הרכישה בביט</span><div id="bitcom-button-container"></div></div>');
                                                $(this).attr('disabled', true);
                                                $(this).attr('style', 'cursor: not-allowed');
                                                foodyLoader.detach();
                                                BitPayment.Buttons({
                                                        onCreate: function (openBitPaymentPage) {
                                                            let transaction = {
                                                                transactionSerialId: bitTransactionId,
                                                                paymentInitiationId: bitPaymentInitiationId
                                                            };
                                                            openBitPaymentPage(transaction);
                                                        },
                                                        onApproved: function (details) {
                                                            //after bit payment confirmed
                                                            //startedBitPayment = true;
                                                            foodyAjax({
                                                                action: 'foody_bitcom_transaction_complete',
                                                                data: {
                                                                    paymentInitiationId: details.paymentInitiationId,
                                                                    memberData: data_of_member,
                                                                    couponId: couponAndPriceObj.coupon_id,
                                                                    couponType: couponAndPriceObj.coupon_type,
                                                                    couponCode: couponAndPriceObj.coupon
                                                                }
                                                            }, function () {
                                                                // show approval page
                                                                alert('nice...');
                                                            });
                                                        },
                                                        style: {
                                                            height: buttonHeight
                                                        }
                                                    }
                                                ).render('#bitcom-button-container');
                                            }
                                        }
                                    }
                                );


                                /** create invoice after purchase been verified */

                                // foodyAjax({
                                //     action: 'foody_create_and_send_invoice',
                                //     data: {
                                //     }
                                // }, function () {
                                //     alert('nice...');
                                // });


                            } else {
                                validate_fields(email, firstName, lastName, phone, termsAccepted);
                            }
                        })
                        ;
                    }

                    if ($('.credit-card-pay').length) {
                        $('.credit-card-pay').on('click', function () {
                            let email = $('#email').val().length != 0 && !$('#email').hasClass('error') ? $('#email').val() : false;
                            let firstName = $('#first-name').val().length != 0 && !$('#first-name').hasClass('error') ? $('#first-name').val() : false;
                            let lastName = $('#last-name').val().length != 0 && !$('#last-name').hasClass('error') ? $('#last-name').val() : false;
                            let phone = $('#phone-number').val().length != 0 && !$('#phone-number').hasClass('error') ? $('#phone-number').val() : false;

                            let termsAccepted = $('.newsletter-and-terms #terms').prop('checked');
                            if (termsAccepted && email && firstName && lastName && phone) {
                                let couponAndPriceObj = checkCouponAndGetCouponAndPrice(used_coupon_details, price);
                                let mailInvoice = $(this).attr('data-invoice-mail').length != 0 ? $(this).attr('data-invoice-mail') : '';
                                let mailNotice = mailInvoice != '' ? '<span class="invoice-notice">*במידה ותרצה לשנות את שם החשבונית יש ליצור קשר במייל ' + '<a href="mailto:' + mailInvoice + '">' + mailInvoice + '</a></span>' : '';
                                let thankYou = $(this).attr('data-thank-you').length != 0 ? $(this).attr('data-thank-you') : '';
                                let enableMarketing = $('.newsletter-and-terms #newsletter').prop('checked') ? 'מאשר קבלת דואר' : 'לא מאשר קבלת דואר';
                                // let itemName = $(this).data('item-name');
                                let link = $(this).attr('data-link') + '?ExtCUserEmail=' + email + '&ExtCInvoiceTo=' + firstName + ' ' + lastName + '&ExtMobilPhone=' + phone + '&SuccessRedirectUrl=' + thankYou + '&custom_field_10=' + enableMarketing;

                                let iframe = '<iframe id="card-pay-frame" src="' + link + '" style="width: 100%;\n' +
                                    'height: auto;\n' +
                                    'min-height: 1500px;\n' +
                                    'padding-top: 3%;\n' +
                                    'border: none;" scrolling="no"></iframe>';


                                $('.cover-section').remove();
                                $('.bottom-image').remove();
                                $('.form-section').replaceWith(iframe);
                                $('#card-pay-frame').after(mailNotice);
                            } else {
                                validate_fields(email, firstName, lastName, phone, termsAccepted);
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
            }
        }
    });

    // $(window).on('beforeunload', function (event) {
    //     if (startedBitPayment == false) {
    //         return 'אם תצא כעת ייתכן והעסקה בביט לא תושלם, האם תרצה לצאת בכל זאת?';
    //         // event.preventDefault();
    //         let choice = confirm('אם תצא כעת ייתכן והעסקה בביט לא תושלם, האם תרצה לצאת בכל זאת?');
    //         if (!choice) {
    //             event.preventDefault();
    //             startedBitPayment = true;
    //         }
    //     }
    // })

    function redeemCoupon() {
        let foodyLoader = new FoodyLoader({container: $('#course-register-form')});
        let couponCode = $('#coupon-input').val();
        if (couponCode.length) {
            foodyLoader.attach();
            foodyAjax({
                action: 'foody_get_coupon_value',
                data: {
                    course_id: $('#redeem-coupon').attr('data-course-id'),
                    coupon_code: couponCode.trim(),
                    course_name: $('#redeem-coupon').attr('data-course-name')
                }
            }, function (err, data) {
                if (err) {
                    console.log(err)
                } else {
                    if (typeof data.data.new_price != 'undefined' && (typeof data.data.id != 'undefined' && data.data.id != null) && (typeof data.data.couponType != 'undefined' && data.data.couponType != null)) {
                        let discounted_price = Math.floor(data.data.new_price);
                        $('#coupon-input')[0].value = '';
                        $('#coupon-input').remove();
                        $('#redeem-coupon').remove();
                        $('#course-price')[0].innerText = discounted_price;
                        $('.coupon-line')[0].innerText = "ערך הקופון ירד מהמחיר";
                        foodyLoader.detach();
                        used_coupon_details =  {'coupon': couponCode, 'discounted_price': discounted_price, 'coupon_id': data.data.id, 'coupon_type': data.data.couponType};
                        return;
                    } else {
                        foodyLoader.detach();
                        alert('קופון לא זמין');
                        used_coupon_details = {'coupon': null, 'discounted_price': data.data.price};
                        return;
                    }
                }

            });
        }
    }
});

function get_current_date() {
    let today = new Date();
    let dd = String(today.getDate()).padStart(2, '0');
    let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    let yyyy = today.getFullYear();

    return yyyy + '-' + mm + '-' + dd;
}

function validate_fields(email, firstName, lastName, phone, termsAccepted) {
    let fields = {
        '#email': email,
        '#first-name': firstName,
        '#last-name': lastName,
        '#phone-number': phone,
        '#terms': termsAccepted
    };
    for (let field in fields) {
        if (!fields[field]) {
            if (field == '#terms') {
                let errorMsg = '<span class="terms-error">' + '*אנא אשר/י את תנאי השימוש' + '</span>';
                if ($('.terms-error').length == 0) {
                    $('.newsletter-and-terms').after(errorMsg);
                }
            } else {
                $("#course-register-form").validate().element(field);
            }
        } else {
            $('.terms-error').remove();
        }
    }
}



function getCoursePrice() {
    foodyAjax({
        action: 'foody_get_course_price',
        data: {
            course_id: $('.course-information').attr('data-course-id'),
        }
    }, function (err, data) {
        if (err) {
            console.log(err)
        } else {
            if (data.data.course_price) {
                return data.data.course_price;
            }
        }
    });
}

function checkCouponAndGetCouponAndPrice(used_coupon_details, price) {
    let coupon = used_coupon_details != null && typeof (used_coupon_details.coupon) != "undefined" ? used_coupon_details.coupon : null;
    let discounted_price = used_coupon_details != null && typeof (used_coupon_details.discounted_price) != "undefined" ? used_coupon_details.discounted_price : null;
    let coupon_id = used_coupon_details != null && typeof (used_coupon_details.coupon_id) != "undefined" ? used_coupon_details.coupon_id : null;
    let coupon_type = used_coupon_details != null && typeof (used_coupon_details.coupon_type) != "undefined" ? used_coupon_details.coupon_type : null;

    if (used_coupon_details != null) {
        // didn't enter a coupon
        price = discounted_price;
    }

    return {'coupon': coupon, 'price': price, 'coupon_id': coupon_id, 'coupon_type': coupon_type};
}

function removeSpacesAndDashFromPhone(phone) {
    let phoneResult = phone;
    if (phone.indexOf("-") >= 0) {
        phoneResult = phone.replace('-', '');
        phoneResult = phoneResult.replace(/\s/g, '');
    }

    return phoneResult;
}
