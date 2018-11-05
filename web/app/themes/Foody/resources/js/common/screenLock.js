/**
 * Created by moveosoftware on 11/5/18.
 */
document.addEventListener('touchmove', function (event) {
    let bodyClasses = document.body.classList;
    if (bodyClasses.contains('lock') || bodyClasses.contains('side-active')) {

        let $target = $(event.target);
        if ($target.parent('header').length == 0) {
            event.preventDefault();
        }
    }
}, {passive: false});

module.exports = function (lock, overlay) {
    let lockClasses = 'lock';
    if (overlay) {
        lockClasses = `${lockClasses} side-active`;
    }

    if (lock) {
        jQuery('html,body').addClass(lockClasses);
    } else {
        jQuery('html,body').removeClass(lockClasses);
    }
};
