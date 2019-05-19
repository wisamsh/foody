jQuery(document).ready(($) => {

    let textNormalizer = function (value) {
        return $.trim(value);
    };

    let $approvalsContainer = jQuery('.campaign-approvals-container');
    if ($approvalsContainer.length) {
        let $form = $("form#campaign-approvals", $approvalsContainer);
        $form.validate({
            rules: {
                'extended-campaign-terms': {
                    required: '#extended-campaign-terms:checked'
                },
                street: {
                    required: true,
                    normalizer: textNormalizer
                },
                'street-number': {
                    required: true,
                    normalizer: textNormalizer
                },
                city: {
                    required: true,
                    normalizer: textNormalizer
                },
                birthday: {
                    required: true
                },
                gender: {
                    required: true
                }
            },
            messages: {
                street: 'כתובת הינה שדה חובה',
                'street-number': 'מספר בית הינו שדה חובה',
                city: 'עיר הינה שדה חובה',
                birthday: 'תאריך לידה הינו שדה חובה',
                gender: 'מין הינו שדה חובה',
                'extended-campaign-terms': foodyGlobals.messages.registration.termsError
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

                $approvalsContainer.block({message: ''});

                foodyAjax({
                    action: 'foody_edit_user_extended_campaign_approvals',
                    data: {
                        street: $('#campaign-approvals #street', $approvalsContainer).val(),
                        'street-number': $('#campaign-approvals #street-number', $approvalsContainer).val(),
                        city: $('#campaign-approvals #city', $approvalsContainer).val(),
                        birthday: $('#campaign-approvals #birthday', $approvalsContainer).val(),
                        gender: $('#campaign-approvals input[name=gender]:checked', $approvalsContainer).val(),
                        'extended-campaign-terms': $('#campaign-approvals #extended-campaign-terms', $approvalsContainer).prop('checked')
                    }
                }, function (err, data) {
                    if (err) {
                        if (err.responseJSON && err.responseJSON.data && err.responseJSON.data[0] && err.responseJSON.data[0].message) {
                            //TODO: analytics
                            // eventCallback('', 'רישום לאתר', 'רישום לדיוור נכשל', foodyGlobals['user']['social_type'], 'הודעה', err.responseJSON.data[0].message);
                        }
                    } else {
                        //TODO: analytics
                        // eventCallback('', 'רישום לאתר', 'רישום לדיוור הצליח', foodyGlobals['user']['social_type'], 'רישום לדיוור', marketingAnalyticsText);
                        $approvalsContainer.unblock();
                        // let $redirect = $('input[name="redirect"]');
                        // if ($redirect.length && $redirect.val() == 1) {
                        window.location.href = foodyGlobals.campaign_url ? foodyGlobals.campaign_url.url : '/';
                        // }
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