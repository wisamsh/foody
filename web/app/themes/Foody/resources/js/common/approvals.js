jQuery(document).ready(($) => {
    let $approvalsContainer = $('.approvals-container');
    if ($approvalsContainer.length) {
        let $form = $("form#approvals", $approvalsContainer);
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

                $approvalsContainer.block({message: ''});

                foodyAjax({
                    action: 'foody_edit_user_approvals',
                    data: {
                        marketing: $('#approvals #check-marketing', $approvalsContainer).prop('checked'),
                        e_book: $('#approvals #check-e-book', $approvalsContainer).prop('checked')
                    }
                }, function () {
                    $approvalsContainer.unblock();
                    let $redirect = $('input[name="redirect"]');
                    // noinspection EqualityComparisonWithCoercionJS
                    if ($redirect.length && $redirect.val() == 1) {
                        window.location.href = '/';
                    }
                });
            }
        });
    }
});