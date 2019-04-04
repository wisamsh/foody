/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');


jQuery(document).ready(($) => {

    // sidebar filter
    let filter = new FoodySearchFilter({
        selector: '.homepage #accordion-foody-filter',
        grid: '#homepage-feed',
        cols: 2,
        searchButton: '.show-recipes',
        page: '.page-template-homepage',
        context: 'homepage',
        contextArgs: [],
    });

    // search and filter pager
    let pager = new FoodyContentPaging({
        context: 'homepage',
        contextArgs: [],
        filter: filter,
        sort: '#sort-homepage-feed'
    });

    let $approvalsPopup = $('#approvals-modal');
    if ($approvalsPopup.length) {
        $approvalsPopup.modal('show');

        $('#approvals .md-checkbox label').on('click', function () {
            let $input = $(this).prev('input[type="checkbox"]');
            let checked = $input.prop('checked');
            $input.prop('checked', checked);
        });

        let $form = $("form#approvals");
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

                let $body = $('.modal-content', $approvalsPopup);
                $body.block({message: ''});

                foodyAjax({
                    action: 'foody_edit_user_approvals',
                    data: {
                        marketing: $('#approvals #check-marketing').prop('checked'),
                        e_book: $('#approvals #check-e-book').prop('checked')
                    }
                }, function () {
                    $body.unblock();
                    $approvalsPopup.modal('hide');
                });
            }
        });

        $approvalsPopup.on('hide.bs.modal', function () {
            let ajaxSettings = {
                action: 'foody_edit_user_approvals_viewed',
                data: {
                    'seen_approvals': true
                }
            };

            foodyAjax(ajaxSettings,function (err) {
                if (err){}
            })
        });
    }
});