/**
 * Created by moveosoftware on 11/5/18.
 */

let levels = [
    'primary',
    'secondary',
    'success',
    'danger',
    'warning',
    'info',
    'light',
    'dark'
];

let $alertPlaceholder = $('#alert-placeholder');

if (!$alertPlaceholder.length) {

    $('body').append('<div id="alert-placeholder"></div>');
}

module.exports = function (level, message) {

    if (!levels.includes(level)) {
        level = levels[0];
    }

    let myAlert = `<div role="alert" class="alert foody-alert alert-dismissible alert-${level}"><span>${message}</span><a class="close" data-dismiss="alert">×</a></div>`;

    $('#content').prepend(myAlert);

    let alertEl = $('.alert');
    alertEl.addClass('foody-show');

    setTimeout(function () {
        alertEl.removeClass('foody-show');
        setTimeout(function () {
            alertEl.remove();
        },500)
    },4000);


    // $(".alert").delay(4000).slideUp(200, function() {
    //     $(this).alert('close');
    // });
};