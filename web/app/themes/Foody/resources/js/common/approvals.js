jQuery(document).ready(($) => {
    let $approvalsContainer = $('.approvals-container');
    if ($approvalsContainer.length) {
        let FoodyLoader = require('./foody-loader');
        let $form = $("form#approvals", $approvalsContainer);
        let foodyLoader = new FoodyLoader({container: $approvalsContainer});
        $form.validate({
            rules: {
                marketing: {
                    required: '#check-e-book:checked'
                }
            },
            messages: {
                marketing: foodyGlobals.messages.registration.eBookError
            },
            errorPlacement: function (error, element) {
                if (element.attr("type") == "checkbox") {
                    let parent = $(element).parent('.md-checkbox');
                    error.insertBefore(parent);
                }
                else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                if (jQuery(form).siblings('.foody-loader').length) {
                    return;
                }
                foodyLoader.attach();

                foodyAjax({
                    action: 'foody_edit_user_approvals',
                    data: {
                        marketing: $('#approvals #check-marketing', $approvalsContainer).prop('checked'),
                        e_book: $('#approvals #check-e-book', $approvalsContainer).prop('checked')
                    }
                }, function (err, data) {
                    if (err) {
                        if (err.responseJSON && err.responseJSON.data && err.responseJSON.data[0] && err.responseJSON.data[0].message) {
                            eventCallback('', 'רישום לאתר', 'רישום לדיוור נכשל', foodyGlobals['user'] ? foodyGlobals['user']['social_type']: 'אין משתמש', 'הודעה', err.responseJSON.data[0].message);
                        }
                    } else {
                        let marketingAnalyticsText = 'לא נרשם';
                        if (data.marketing) {
                            marketingAnalyticsText = 'נרשם';
                            if (data.e_book) {
                                marketingAnalyticsText = 'נרשם פלוס ספר';
                            }
                        } else if (data.e_book) {
                            marketingAnalyticsText = 'לא נרשם פלוס ספר';
                        }
                        eventCallback('', 'רישום לאתר', 'רישום לדיוור הצליח', foodyGlobals['user'] ? foodyGlobals['user']['social_type'] : 'אין משתמש', 'רישום לדיוור', marketingAnalyticsText);
                        foodyLoader.detach();
                        let $redirect = $('input[name="redirect"]');
                        // noinspection EqualityComparisonWithCoercionJS
                        if ($redirect.length && $redirect.val() == 1) {
                            if (data && data.data && data.data['go-to']) {
                                window.location.href = data.data['go-to'];
                            } else {
                                window.location.href = '/';
                            }
                        }
                    }
                });
            }
        });
    }
});


/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 * @param action
 * @param label
 * @param cdDesc
 * @param cdValue
 */
function eventCallback(event, category, action, label, cdDesc, cdValue) {

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        cdDesc,
        cdValue,
        ''
    );
}