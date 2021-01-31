/**
 * Created by moveosoftware on 10/9/18.
 */
let FoodyLoader = require('../common/foody-loader');
let price;
let used_coupon_details = null;
let mobileOS = foodyGlobals.isMobile ? getMobileOperatingSystem() : false;
let calUser = false;

jQuery(document).ready(($) => {
    window.scroll(0, 0);
    if ($('#coupon-input').length) {
        let expiredModalElm = createAlertModal('coupon-dialog-expired', 'פג תוקף הקופון');
        let unavailableModalElm = createAlertModal('coupon-dialog-unavailable', 'הקופון לא זמין');

        $('body').append(expiredModalElm);
        $('body').append(unavailableModalElm);
    }
    if (foodyGlobals.page_template_name == "foody-course-register") {
        foodyAjax({
            action: 'foody_get_course_price',
            data: {
                course_id: $('.course-information').attr('data-course-id'),
            },
            isIos: mobileOS
        }, function (err, data) {
            if (err) {
                console.log(err)
            } else {
                if (data.data.course_price) {
                    price = data.data.course_price;
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

                    $('#redeem-coupon').on('click', function () {
                        redeemCoupon();
                    });

                    $('#course-register-form .checkbox').on('click', function () {
                        let $input = $(this).prev('input[type="checkbox"]');
                        let checked = $input.prop('checked');
                        $input.prop('checked', checked);
                    });

                    let textNormalizer = function (value) {
                        return $.trim(value);
                    };

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
                                city:{
                                    required: 'שם עיר הינו שדה חובה'
                                },
                                street:{
                                    required: 'שם רחוב הינו שדה חובה'
                                },
                                building_number:{
                                    required: 'מספר בניין הינו שדה חובה'
                                },
                                 apt:{
                                    required: 'מספר דירה הינו שדה חובה'
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
                                let inputsObj = get_all_form_inputs(this);

                                if (inputsObj.termsAccepted && inputsObj.email && inputsObj.firstName && inputsObj.lastName && inputsObj.phone && inputsObj.courseName) {
                                    // temp => only send data to members plugin
                                    let couponAndPriceObj = checkCouponAndGetCouponAndPrice(used_coupon_details, price);
                                    let foodyLoader = new FoodyLoader({
                                        container: $('.button-container'),
                                        id: 'buttons-loader'
                                    });
                                    let urlParams = getUrlVars();

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
                                        'email': inputsObj.email,
                                        'first_name': inputsObj.firstName,
                                        'last_name': inputsObj.lastName,
                                        'phone': inputsObj.phone,
                                        'purchase_date': get_current_date(),
                                        'enable_marketing': inputsObj.enableMarketing,
                                        'course_name': inputsObj.courseName,
                                        'course_id': urlParams.course_id,
                                        'price': couponAndPriceObj.price,
                                        'payment_method': 'ביט',
                                        'transaction_id': '-1',
                                        'coupon': couponAndPriceObj.coupon,
                                        'status': 'pending',
                                        'payment_method_id': '-1'
                                    };
                                    foodyLoader.attach({topPercentage: 20});
                                    foodyAjax({
                                            action: 'foody_start_bit_pay_process',
                                            data: {
                                                email: inputsObj.email,
                                                first_name: inputsObj.firstName,
                                                last_name: inputsObj.lastName,
                                                price: couponAndPriceObj.price,
                                                item_name: inputsObj.courseName,
                                                memberData: data_of_member,
                                                isMobile: mobileOS,
                                                thankYou: inputsObj.thankYou
                                            }
                                        }, function (err, data) {
                                            if (err) {
                                                console.log(err);
                                                foodyLoader.detach();
                                            } else {
                                                if (typeof data.data.single_payment_ids != 'undefined' && typeof data.data.single_payment_ids.mobileSchema != 'undefined' && data.data.single_payment_ids.mobileSchema) {
                                                    $('.button-container').after('<div class="bit-button-container"><span class="bit-btn-text">לחץ כאן להשלמת תשלום בביט</span><span class="bit-notice-text">*חשוב לא לסגור את העמוד עד סיום הרכישה בביט</span><a href="' + data.data.single_payment_ids.mobileSchema + '" id="bitcom-button-container-mobile"></a></div>');
                                                    foodyLoader.detach();
                                                } else if (typeof data.data.single_payment_ids != 'undefined' && typeof data.data.single_payment_ids != 'undefined' && data.data.single_payment_ids) {
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
                                                                window.location = inputsObj.thankYou + '&payment_method=ביט&status=approved&paymentInitiation=' + bitPaymentInitiationId;
                                                            },
                                                            onCancel: function (details) {
                                                                // Show a Cancellation Page
                                                                window.location = inputsObj.thankYou + '&payment_method=ביט&status=canceled&paymentInitiation=' + bitPaymentInitiationId;
                                                            },
                                                            onTimeout: function (details) {
                                                                location.reload();
                                                            },

                                                            style: {
                                                                height: buttonHeight
                                                            }
                                                        }
                                                    ).render('#bitcom-button-container');
                                                } else {
                                                    if (typeof data.success != 'undefined' && !data.success && data.data.msg) {
                                                        let expiredModalElm = createAlertModal('bit-error-dialog', data.data.msg);
                                                        $('body').append(expiredModalElm);
                                                        foodyLoader.detach();
                                                        $("#bit-error-dialog").modal({backdrop: true});
                                                    }
                                                }
                                            }
                                        }
                                    );
                                } else {
                                    // validate_fields(inputsObj.email, inputsObj.firstName, inputsObj.lastName, inputsObj.phone, inputsObj.termsAccepted);
                                    validate_fields({
                                        'email': inputsObj.email,
                                        'firstName': inputsObj.firstName,
                                        'lastName': inputsObj.lastName,
                                        'phone': inputsObj.phone,
                                        'termsAccepted': inputsObj.termsAccepted
                                    });
                                }
                            })
                            ;
                        }

                        if ($('.credit-card-pay').length) {
                            $('.credit-card-pay').on('click', function () {
                                if (typeof $(this).prop('disabled') == 'undefined' || $(this).prop('disabled') === "false") {
                                    let inputsObj = get_all_form_inputs(this);
                                    let urlParams = getUrlVars();

                                    if (inputsObj.termsAccepted && inputsObj.email && inputsObj.firstName && inputsObj.lastName && inputsObj.phone && is_valid_address(inputsObj)) {
                                        let foodyLoader = new FoodyLoader({
                                            container: $('.button-container'),
                                            id: 'buttons-loader'
                                        });
                                        let couponAndPriceObj = checkCouponAndGetCouponAndPrice(used_coupon_details, price);
                                        let mailInvoice = $(this).attr('data-invoice-mail').length != 0 ? $(this).attr('data-invoice-mail') : '';

                                        let data_of_member = {
                                            'email': inputsObj.email,
                                            'first_name': inputsObj.firstName,
                                            'last_name': inputsObj.lastName,
                                            'phone': inputsObj.phone,
                                            'purchase_date': get_current_date(),
                                            'enable_marketing': inputsObj.enableMarketing,
                                            'course_name': inputsObj.courseName,
                                            'course_id': urlParams.course_id,
                                            'price': couponAndPriceObj.price,
                                            'payment_method': 'כרטיס אשראי',
                                            'transaction_id': '-1',
                                            'coupon': couponAndPriceObj.coupon,
                                            'status': 'pending',
                                            'payment_method_id': '-1'
                                        };

                                        if(calUser){
                                            data_of_member['address'] = inputsObj.city + " " + inputsObj.street + " " + inputsObj.building_number + ", " + inputsObj.apt;
                                        }

                                        foodyLoader.attach({topPercentage: 20});
                                        foodyAjax({
                                            action: 'foody_start_cardcom_pay_process',
                                            data: {
                                                memberData: data_of_member,
                                                isMobile: mobileOS,
                                                thankYou: inputsObj.thankYou
                                            }
                                        }, function (err, data) {
                                            if (err) {
                                                console.log(err);
                                                foodyLoader.detach();
                                            } else {
                                                foodyLoader.detach();
                                                let link = data.data.iframe_url;
                                                let iframe = '<iframe  runat="server" id="card-pay-frame" src="' + link + '" style="width: 100%;\n' +
                                                    'height: 1035px;\n' +
                                                    'max-height: 1500px;\n' +
                                                    'min-height: 700px;\n' +
                                                    'padding-top: 3%;\n' +
                                                    'border: none;" onload="scroll(0,0);" scrolling="no"></iframe>';


                                                $('.cover-section').remove();
                                                $('.bottom-image').remove();
                                                window.scroll(0, 0);
                                                $('.form-section').replaceWith(iframe);
                                                // $('#card-pay-frame').after(mailNotice);
                                            }
                                        });
                                    } else {
                                        if (calUser) {
                                            validate_fields({
                                                'email': inputsObj.email,
                                                'firstName': inputsObj.firstName,
                                                'lastName': inputsObj.lastName,
                                                'phone': inputsObj.phone,
                                                'termsAccepted': inputsObj.termsAccepted,
                                                'city': inputsObj.city,
                                                'street': inputsObj.street,
                                                'building_number': inputsObj.building_number,
                                                'apt': inputsObj.apt,
                                            });
                                        }
                                        else {
                                            validate_fields({
                                                'email': inputsObj.email,
                                                'firstName': inputsObj.firstName,
                                                'lastName': inputsObj.lastName,
                                                'phone': inputsObj.phone,
                                                'termsAccepted': inputsObj.termsAccepted
                                            });
                                        }
                                    }
                                }
                            });
                            $('#card-pay-frame').load(function () {
                                this.attr('src', link);
                            })

                        }
                    }
                }
            }
        });

        function is_valid_address(inputsObj) {
            if (calUser) {
                if ((typeof inputsObj.city !== 'undefined' && inputsObj.city) && (typeof inputsObj.street !== 'undefined' && inputsObj.street) && (typeof inputsObj.building_number !== 'undefined' && inputsObj.building_number) && (typeof inputsObj.apt !== 'undefined' && inputsObj.apt)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }

        function redeemCoupon() {
            let foodyLoader = new FoodyLoader({container: $('.coupon-and-price-container'), id: 'coupon-loader'});
            let couponCode = $('#coupon-input').val();
            if (couponCode.length) {
                $('.credit-card-pay').prop('disabled', "true");
                foodyLoader.attach({topPercentage: 20});
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
                            let discounted_price = getRoundedPrice(data.data.new_price);
                            $('#coupon-input')[0].value = '';
                            $('#coupon-input').remove();
                            $('#redeem-coupon').remove();
                            $('#course-price')[0].innerText = discounted_price;
                            $('.coupon-line')[0].innerText = "ערך הקופון ירד מהמחיר";

                            used_coupon_details = {
                                'coupon': couponCode,
                                'discounted_price': discounted_price,
                                'coupon_id': data.data.id,
                                'coupon_type': data.data.couponType
                            };
                            foodyLoader.detach();
                            $('.credit-card-pay').prop('disabled', "false");
                            return;
                        } else {
                            used_coupon_details = {'coupon': null, 'discounted_price': data.data.price};
                            foodyLoader.detach();
                            if (typeof data.data.msg != 'undefined' && data.data.msg == 'expired') {
                                $("#coupon-dialog-expired").modal({backdrop: true});
                            } else {
                                $("#coupon-dialog-unavailable").modal({backdrop: true});
                            }
                            $('.credit-card-pay').prop('disabled', "false");
                        }
                    }

                });
            }
        }
    }
    if (foodyGlobals.page_template_name = "foody-courses-thank-you") {
        window.scroll(0, 0);
    }
});

function get_current_date() {
    let today = new Date();
    let dd = String(today.getDate()).padStart(2, '0');
    let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    let yyyy = today.getFullYear();

    return yyyy + '-' + mm + '-' + dd;
}

// function validate_fields(email, firstName, lastName, phone, termsAccepted) {
function validate_fields(fieldsToValidate) {
    let fields = {
        '#email': fieldsToValidate['email'],
        '#first-name': fieldsToValidate['firstName'],
        '#last-name': fieldsToValidate['lastName'],
        '#phone-number': fieldsToValidate['phone'],
        '#terms': fieldsToValidate['termsAccepted']
    };

    if (calUser) {
        fields['#city'] = fieldsToValidate['city'];
        fields['#street'] = fieldsToValidate['street'];
        fields['#building_number'] = fieldsToValidate['building_number'];
        fields['#apt'] = fieldsToValidate['apt'];
    }

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
            if (field == '#terms') {
                $('.terms-error').remove();
            }
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

function get_all_form_inputs(button_pressed) {
    let _email = $('#email').val().length != 0 && !$('#email').hasClass('error') ? $('#email').val() : false;
    let _firstName = $('#first-name').val().length != 0 && !$('#first-name').hasClass('error') ? $('#first-name').val() : false;
    let _lastName = $('#last-name').val().length != 0 && !$('#last-name').hasClass('error') ? $('#last-name').val() : false;
    let _phone = $('#phone-number').val().length != 0 && !$('#phone-number').hasClass('error') ? removeSpacesAndDashFromPhone($('#phone-number').val()) : false;
    let _enableMarketing = $('.newsletter-and-terms #newsletter').prop('checked') ? true : false;
    let _courseName = $(button_pressed).attr('data-item-name').length != 0 ? $(button_pressed).attr('data-item-name') : false;
    let _thankYou = $(button_pressed).attr('data-thank-you').length != 0 ? $(button_pressed).attr('data-thank-you') : '';
    let _termsAccepted = $('.newsletter-and-terms #terms').prop('checked');

    let input_data = {
        email: _email,
        firstName: _firstName,
        lastName: _lastName,
        phone: _phone,
        enableMarketing: _enableMarketing,
        courseName: _courseName,
        thankYou: _thankYou,
        termsAccepted: _termsAccepted
    };

    if ($('.button-container .credit-card-pay').length && $('.button-container .credit-card-pay').attr('data-is-cal').length && $('.button-container .credit-card-pay').attr('data-is-cal') === "1") {
        calUser = true;
        input_data['city'] = $('.form-container #city').length != 0 ? $('.form-container #city').val() : false;
        input_data['street'] = $('.form-container #street').length != 0 ? $('.form-container #street').val() : false;
        input_data['building_number'] = $('.building-details #building_number').length != 0 ? $('.building-details #building_number').val() : false;
        input_data['apt'] = $('.building-details #apt').length != 0 ? $('.building-details #apt').val() : false;
    }

    return input_data;
}

function getMobileOperatingSystem() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    if (/android/i.test(userAgent)) {
        return "Android";
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "iOS";
    }

    return false;
}

function getRoundedPrice(price) {
    let roundedPriceRes = price.toFixed(1);

    if (roundedPriceRes < 1) {
        roundedPriceRes = 1;
    }

    return roundedPriceRes;
}

function createAlertModal(id, msg) {
    let modalElm = '<div class="modal fade" id="' + id + '" role="dialog">\n' +
        '    <div class="modal-dialog">\n' +
        '    \n' +
        '      <!-- Modal content-->\n' +
        '      <div class="modal-content">\n' +
        '        <div class="modal-header">\n' +
        '          <button type="button" class="close" data-dismiss="modal">&times;</button>\n' +
        '          <h4 class="modal-title">שגיאה</h4>\n' +
        '        </div>\n' +
        '        <div class="modal-body">\n' +
        '          <p>' + msg + '</p>\n' +
        '        </div>\n' +
        '      </div>\n' +
        '      \n' +
        '    </div>\n' +
        '  </div>';

    return modalElm;
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}