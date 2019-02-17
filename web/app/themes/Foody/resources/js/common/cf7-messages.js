const foodyAlert = require('./alerts');

module.exports = function (container) {

    let wpcf7Elm = $('.wpcf7', container);

    wpcf7Elm.each(function () {
        this.addEventListener('wpcf7submit', function (event) {

            let details = event.detail.apiResponse;

            console.log(event);
            event.preventDefault();

            if (details.status === 'mail_sent') {

                $(this).append($(`<div class="foody-form-message">${details.message}</div>`));

                $('.foody-form-message',this).delay(2000).fadeOut();

            }

        }, false);
    });
};