/**
 * Created by moveosoftware on 11/5/18.
 */
document.addEventListener('touchmove', function (event) {
    let bodyClasses = document.body.classList;
    if (bodyClasses.indexOf('lock') != -1 || bodyClasses.indexOf('side-active') != -1) {
        event.preventDefault();
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
