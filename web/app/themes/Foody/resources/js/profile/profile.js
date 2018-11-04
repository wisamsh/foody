/**
 * Created by moveosoftware on 9/29/18.
 */

let toggleFollowed = require('../common/follow');
let FoodySearchFilter = require('../common/foody-search-filter');

jQuery(document).ready(($) => {



    // Followed topics list
    $('.managed-list li .close').click(function () {

        let $parent = $(this).parent('li');
        let id = $parent.data('id');
        let type = $parent.data('type');


        let eventName = null;

        if (type == 'followed_channels') {
            eventName = 'remove channel';
        } else if (type == 'followed_authors') {
            eventName = 'remove creator';
        }

        if (eventName) {
            analytics.event(eventName, {
                id: id,
                title: $('a', $parent).text()
            });
        }


        toggleFollowed(id, type, function (error) {

            if (error) {
                // TODO handle
            } else {


                $parent.fadeOut({
                    duration: 300,
                    complete: function () {
                        $parent.detach();
                    }
                });
            }
        })
    });


    // Foody search and filter
    new FoodySearchFilter({
        selector: '.page-template-profile #accordion-foody-filter',
        grid: '.my-channels-grid',
        cols: 1
    });
    new FoodySearchFilter({
        selector: '.page-template-profile #accordion-foody-filter',
        grid: '.my-recipes-grid',
        cols: 1
    });

    // Tab switch analytics
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

        let tab = $(e.target).attr('href');

        tab = tab.replace('#', '');

        let eventName = null;
        if (tab == 'my-channels-recipes') {
            eventName = 'my channel recipes';
        } else if (tab == 'my-recipes') {
            eventName = 'my recipes button';
        }

        if (eventName != null) {

            analytics.event(eventName);
        }

    });

    // Nested tabs fix
    let tabs = [
        '#edit-user-details',
        '#change-password',
        '#user-content',
    ];

    let tabsSelector = tabs.map((selector) => {
        return `a[href="${selector}"]`;
    }).join(',');

    $(tabsSelector).on('shown.bs.tab', function (e) {

        let $this = $(this);

        let target = $this.attr('href');

        let others = tabs.filter((tab) => {
            return tab != target;
        });

        others.forEach((selector) => {
            $(selector).removeClass('active');
            $(selector).removeClass('show');

            let link = 'a[href="' + selector + '"]';

            $(link).removeClass('active');
            $(link).removeClass('show');

        });

        if (foodyGlobals.isMobile) {
            let $mobileChannels = $('.profile-top .my-channels');
            if (target == '#user-content') {
                $mobileChannels.removeClass('d-none').addClass('d-block');
            } else {
                $mobileChannels.addClass('d-none').removeClass('d-block');
            }
        }
    });


    // Change password form validation
    $("#password-reset").validate({
        rules: {
            current_password: {
                required: true,
                password: true,
                minlength: 8,
            },
            password: {
                required: true,
                minlength: 8,
                password: true,
            },
            password_confirmation: {
                required: true,
                equalTo: '#password[name="password"]',
            }
        },
        messages: {
            current_password: {
                required: 'סיסמא נוכחית הינו שדה חובה',
                password: 'סיסמא אינה תקינה'
            },
            password: {
                required: 'סיסמא חדשה הינו שדה חובה',
                password: 'סיסמא אינה תקינה'
            },
            password_confirmation: {
                required: 'ווידוא סיסמא הינו שדה חובה',
                equalTo: 'סיסמאות אינן תואמות',
            }
        }, submitHandler: function (form) {
            form.submit();
        }
    });


    let handler = formSubmit({
        form: 'form#edit-user-details',
        ajaxUrl: '/wp/wp-admin/admin-ajax.php',
        action: '&action=foody_edit_user',
        ajaxSettings: {
            beforeSend: function (xhr) {

            },
            error: function (request, status, error) {
                console.log(error, request.responseJSON);
            },
            success: function (data) {
                console.log('success', data);
            },
            complete: function () {

            }
        }
    });

    let userDetailsValidator = $("form#edit-user-details").validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            phone_number: {
                regex: /^((\+972|972)|0)( |-)?([1-468-9]( |-)?\d{7}|(5|7)[0-9]( |-)?\d{7})/
            }
        },
        messages: {
            first_name: {
                required: 'שם פרטי הינו שדה חובה'
            },
            last_name: {
                required: 'שם משפחה הינו שדה חובה'
            },
            phone_number: {
                regex: 'מספר טלפון אינו תקין'
            }
        },
        submitHandler: (form) => {
            console.log('submitHandler');
            form.submit();
        }
    });




});