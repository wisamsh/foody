/**
 * Created by moveosoftware on 11/5/18.
 */


let foodyAlert = require('../common/alerts');

jQuery(document).ready(($) => {

    let wpcf7Elm = document.querySelector('.wpcf7');

    let $form = $('.wpcf7');

    if($form.length){
        wpcf7Elm.addEventListener('wpcf7submit', function (event) {

            let details = event.detail.apiResponse;

            console.log(event);
            event.preventDefault();

            if(details.status == 'mail_sent'){
                foodyAlert('success',details.message);
            }

        }, false);
    }


});