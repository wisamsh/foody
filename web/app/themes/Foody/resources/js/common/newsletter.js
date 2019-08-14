/**
 * Created by moveosoftware on 13/8/19.
 */

jQuery(document).ready(($) => {

    $('.wpcf7').on('wpcf7invalid', function (event) {

        let messageSelector = $('.invalid .wpcf7-not-valid-tip');
        let responseOutput = $('.invalid .wpcf7-response-output');
        messageSelector.remove();
        responseOutput.remove();
        messageSelector.insertBefore($('.site-footer .invalid .bmd-form-group'));
    });
});

