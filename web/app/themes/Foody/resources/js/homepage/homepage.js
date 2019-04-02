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


        $("form#approvals").validate({
            rules: {
                marketing: {
                    required: '#check-e-book:checked'
                }
            },
            messages: {
                marketing: 'נשמח לשלוח לך את ספר המתכונים, אבל קודם יש לאשר קבלת דואר מאתר Foody'
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
                foodyAjax({
                    action: 'foody_edit_user_approvals',
                    data: {
                        marketing:$('#approvals #check-marketing').val(),
                        e_book:$('#approvals #check-e-book').val(),
                    }
                }, function (err, data) {
                    $approvalsPopup.modal('hide');
                });
            }
        });


    }
});