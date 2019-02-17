/**
 * Created by moveosoftware on 11/5/18.
 */


let foodyAlert = require('../common/alerts');

jQuery(document).ready(($) => {

    let wpcf7Elm = document.querySelector('.page-template-centered-content .wpcf7');


    if (wpcf7Elm) {
        wpcf7Elm.addEventListener('wpcf7submit', function (event) {

            let details = event.detail.apiResponse;
            event.preventDefault();

            if (details.status === 'mail_sent') {
                foodyAlert('success', details.message);
            }

        }, false);
    }
});